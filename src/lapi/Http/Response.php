<?php

namespace Lake\Admin\Lapi\Http;

use Illuminate\Http\Exceptions\HttpResponseException;

use Lake\Admin\Lapi\Contracts\Response as ResponseContract;

/*
 * 响应
 *
 * @create 2020-9-13
 * @author deatil
 */
class Response implements ResponseContract
{
    /*
     * 响应json输出
     * @param boolen $success
     * @param int $code
     * @param string|null $msg
     * @param array|null $data
     * @return string json
     *
     * @create 2020-9-13
     * @author deatil
     */
    public function json($success, $code = 99999, $msg = '', $data = []) 
    {
        $result['success'] = $success;
        $result['code'] = $code;
        $msg ? $result['msg'] = $msg : null;
        $data ? $result['data'] = $data : null;
        
        $app = config('lapi.app');

        $header = [];
        if ($app['allow_origin'] == 1) {
            $header['Access-Control-Allow-Origin']  = '*';
            $header['Access-Control-Allow-Headers'] = 'X-Requested-With,X_Requested_With,Content-Type';
            $header['Access-Control-Allow-Methods'] = 'GET,POST,PATCH,PUT,DELETE,OPTIONS';
        }
        $header['content-type']  = 'application/json';
        
        $result = json_encode($result, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        
        $response = response($result, 200, $header);
        throw new HttpResponseException($response);
    }

}
