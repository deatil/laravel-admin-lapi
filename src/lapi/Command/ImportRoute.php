<?php

namespace Lake\Admin\Lapi\Command;

use ReflectionClass;

use Illuminate\Console\Command;

use Lake\Admin\Lapi\Lib\Doc;
use Lake\Admin\Lapi\Service\Route as RouteService;
use Lake\Admin\Lapi\Model\Url as UrlModel;

/**
 * 导入路由信息
 *
 * > php artisan lapi:import-route
 *
 */
class ImportRoute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lapi:import-route';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'lapi import route\'info.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->import();
        
        $this->info('import route success!');
    }
    
    /**
     * 导入
     * 
     * @create 2020-9-14
     * @author deatil
     */
    protected function import()
    {
        $RouteService = (new RouteService);
        $routes = $RouteService->getRoutes();
        if (empty($routes)) {
            return false;
        }
        
        foreach ($routes as $route) {
            if (!isset($route['prefix']) 
                || $route['prefix'] != 'api'
                || empty($route['method'])
            ) {
                continue;
            }
            
            $methodDoc = $this->formatDoc($route['action']);
            $docInfo = [
                'title' => '',
                'request' => '',
                'response' => '',
                'description' => '',
                'listorder' => 100,
            ];
            if ($methodDoc !== false) {
                $docInfo = array_merge($docInfo, $methodDoc);
            }
            
            if (empty($docInfo['description'])) {
                $docInfo['description'] = json_encode([
                        'action' => $route['action'],
                        'middleware' => implode(',', $route['middleware']->toArray()),
                    ]);
            }
            if (empty($docInfo['title'])) {
                $docInfo['title'] = $route['uri'];
            }
            
            foreach ($route['method'] as $method) {
                $urlInfo = UrlModel::where('slug', $route['name'])
                    ->where('method', $method)
                    ->first();
                if (!empty($urlInfo)) {
                    $data = array_merge($docInfo, [
                            'url' => $route['uri'],
                            'edit_time' => time(),
                            'edit_ip' => request()->ip(),
                        ]);
                    UrlModel::where('id', $urlInfo['id'])
                        ->update($data);
                } else {
                    $data = array_merge($docInfo, [
                        'id' => md5(mt_rand(10000, 99999).time().mt_rand(10000, 99999)),
                        'parentid' => 0,
                        'slug' => $route['name'],
                        'url' => $route['uri'],
                        'method' => $method,
                        'status' => 1,
                        'edit_time' => time(),
                        'edit_ip' => request()->ip(),
                        'add_time' => time(),
                        'add_ip' => request()->ip(),
                    ]);
                    
                    $UrlModel = (new UrlModel);
                    foreach ($data as $column => $value) {
                        $UrlModel->setAttribute($column, $value);
                    }
                    $UrlModel->save();
                }
            }
        }
    }
    
    /**
     * 格式化注释
     * 
     * @create 2020-9-14
     * @author deatil
     */
    protected function formatDoc($action)
    {
        if (empty($action)) {
            return false;
        }
        
        $actions = explode('@', $action);
        if (count($actions) <= 1) {
            return false;
        }
        
        list($actionClass, $actionMethod) = $actions;
        
        $actionReflection = new ReflectionClass($actionClass);
        $methodDocComment = $actionReflection->getMethod($actionMethod)->getDocComment();
        $actionDocComment = $this->parseDoc($methodDocComment);
        
        $actionDocCommentInfo = [
            'title' => $actionDocComment['title'],
            'request' => isset($actionDocComment['request']) ? $actionDocComment['request'] : '',
            'response' => isset($actionDocComment['response']) ? $actionDocComment['response'] : '',
            'description' => isset($actionDocComment['description']) ? $actionDocComment['description'] : '',
            'listorder' => isset($actionDocComment['order']) ? $actionDocComment['order'] : 100,
        ];
        
        return $actionDocCommentInfo;
    }
    
    /**
     * 解析注释
     * 
     * @create 2020-9-14
     * @author deatil
     */
    protected function parseDoc($text)
    {
        $doc = new Doc();
        return $doc->parse($text);
    }
    
}
