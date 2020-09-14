<?php

namespace Lake\Admin\Lapi\Service;

use Illuminate\Http\Exceptions\HttpResponseException;

use Lake\Admin\Lapi\Lib\Sign;
use Lake\Admin\Lapi\Lib\Sha256Sign;

use Lake\Admin\Lapi\Model\App as AppModel;
use Lake\Admin\Lapi\Model\AppLog as AppLogModel;
use Lake\Admin\Lapi\Model\Config as ConfigModel;
use Lake\Admin\Lapi\Model\Url as UrlModel;
use Lake\Admin\Lapi\Model\UrlAccess as UrlAccessModel;

/*
 * API检测
 *
 * @create 2020-9-7
 * @author deatil
 */
class Lapi
{
    /*
     * 检测签名
     *
     * @create 2020-9-7
     * @author deatil
     */
    public function checkApiSign()
    {
        // api设置
        $appConfig = ConfigModel::getList();
        if (isset($appConfig['api_close']) 
            && $appConfig['api_close'] == 1
        ) {
            return $this->errorJson($appConfig['api_close_tip'], 99999);
        }
        config([
            'lapi.app_config' => $appConfig,
        ]);
        
        $data = request()->all();
        if (empty($data)) {
            return $this->errorJson("数据错误", 99);
        }
        
        if (isset($appConfig['open_putlog']) 
            && $appConfig['open_putlog'] == 1
        ) {
            // 记录日志
            $this->createApiLog([
                'app_id' => isset($data['app_id']) ? $data['app_id'] : 'error',
            ]);
        }
        
        if (!isset($data['app_id']) || empty($data['app_id'])) {
            return $this->errorJson("app_id错误", 99);
        }
        $appId = $data['app_id'];

        $app = AppModel::where([
            'app_id' => $appId,
        ])->first();
        if (empty($app) || !$app['status']) {
            return $this->errorJson("授权错误", 97);
        }

        // 签名检测
        if ($app['is_check'] == 1) {
            $userAgent = request()->server('HTTP_USER_AGENT');
            if (empty($userAgent)) {
                return $this->errorJson("客户端错误", 99);
            }
        
            $nonceStr = $data['nonce_str'];
            if (empty($nonceStr)) {
                return $this->errorJson("nonce_str错误", 99);
            }
            if (strlen($nonceStr) != 16) {
                return $this->errorJson("nonce_str格式错误", 99);
            }

            $timestamp = $data['timestamp'];
            if (empty($timestamp)) {
                return $this->errorJson("时间戳错误", 99);
            }
            if (strlen($timestamp) != 10) {
                return $this->errorJson("时间戳格式错误", 99);
            }
            if (time() - $timestamp > (60 * 30)) {
                return $this->errorJson("时间错误，请确认你的时间为正确的北京时间", 99);
            }

            // 验证签名
            if ($app['check_type'] == 'SHA256') {
                $checkSign = Sha256Sign::getInstance();
            } else {
                $checkSign = Sign::getInstance();
            }

            if ($app['sign_postion'] == 'header') {
                $sign = request()->header('sign');
                if (!isset($sign)) {
                    return $this->errorJson("签名错误", 99);
                }
            } else {
                if (!isset($data['sign'])) {
                    return $this->errorJson("签名错误", 99);
                }
                
                $sign = $data['sign'];
            }

            if (empty($sign)) {
                return $this->errorJson("签名错误", 99);
            }
            
            $checkSignData = $data;
            $checkSignKey = $app['app_secret'];
            $checkSignString = $checkSign->makeSign($checkSignData, $checkSignKey);

            if ($checkSignString != $sign) {
                return $this->errorJson("授权验证失败", 99);
            }
        }
        
        config([
            'lapi.app' => $app,
        ]);
        
        $this->checkUrlApiAuth();
    }
    
    /*
     * 检测接口链接权限
     *
     * @create 2020-9-7
     * @author deatil
     */
    public function checkUrlApiAuth()
    {
        $requestMethod = request()->getMethod();
        $requestUrl = \Route::currentRouteName();
        
        $requestUrlInfo = UrlModel::where([
                'slug' => $requestUrl,
            ])
            ->first();
        if (empty($requestUrlInfo) 
            || $requestUrlInfo['status'] != 1
        ) {
            return $this->errorJson("该链接拒绝访问", 99);
        }
        
        if ($requestUrlInfo['method'] != strtoupper($requestMethod)) {
            return $this->errorJson("该链接拒绝访问", 99);
        }
        
        $app = config('lapi.app');
        $requestUrlAccessInfo = UrlAccessModel::where([
                'app_id' => $app['id'],
                'url_id' => $requestUrlInfo['id'],
            ])
            ->first();
        if (empty($requestUrlAccessInfo)) {
            return $this->errorJson("该链接拒绝访问", 99);
        }
        
        $appConfig = config('lapi.app_config');
        if (isset($appConfig['open_putlog']) 
            && $appConfig['open_putlog'] == 1
        ) {
            $LapiAppLogCount = AppLogModel::where([
                    ['app_id', '=', $app['app_id']],
                    ['api', '=', $requestUrl],
                    ['add_time', '>=', (time() - 1)],
                ])
                ->count();
            if ($LapiAppLogCount > intval($requestUrlAccessInfo['max_request'])) {
                return $this->errorJson("请求访问太快了", 99);
            }
        }
    }

    /*
     * 添加日志
     *
     * @create 2020-9-7
     * @author deatil
     */
    public function createApiLog($data = []) 
    {
        if (empty($data)) {
            return false;
        }
        
        $requestUrl = \Route::currentRouteName() ?: '--';
        
        $data = array_merge([
            'id' => md5(time().mt_rand(10000, 99999).mt_rand(100, 999)),
            'api' => $requestUrl,
            'url' => urldecode(request()->getUri()),
            'method' => app()->request->method(),
            'useragent' => request()->server('HTTP_USER_AGENT'),
            'header' => json_encode(request()->header(), JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),
            'payload' => json_encode(request()->all(), JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),
            'content' => request()->getContent(),
            'cookie' => json_encode($_COOKIE, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),
            'add_time' => time(),
            'add_ip' => request()->ip(),
        ], $data);
        
        $status = AppLogModel::insert($data);
        if ($status === false) {
            return false;
        }
        
        return true;
    }
    
    /*
     * 返回错误json
     *
     * @create 2020-8-12
     * @author deatil
     */
    public function errorJson($msg = null, $code = 1, $data = []) 
    {
        return app('lapiJson')->json([
            'success' => false,
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ]);
    }
    
    /*
     * 返回成功json
     *
     * @create 2020-8-12
     * @author deatil
     */
    public function successJson($msg = '获取成功', $data = null, $code = 0) 
    {
        return app('lapiJson')->json([
            'success' => true,
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ]);
    }
}
