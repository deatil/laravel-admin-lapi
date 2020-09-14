## api管理系统


### 项目介绍

*  基于 `laravel-admin` 后台管理框架的api管理系统模块扩展
*  `签名算法` 借鉴于微信支付，具体算法可以查看微信支付文档


### 使用方法 

*  安装
~~~
composer require lake/laravel-admin-lapi

> php artisan admin:import lapi
~~~

*  导入路由信息，解析api控制器注释
~~~
> php artisan lapi:import-route
~~~


### 请求示例 

*  GET 请求
~~~
GET: https://yourdomain.com/api/aaa?app_id=API2020091315292812159&name=aaa&nonce_str=SNejQr2b9RdF1CH1&sign=B28ED49A3EF7CEB615AE735608039562&timestamp=1600095342
~~~

*  POST 请求
~~~
POST: https://yourdomain.com/api/aaa 
body: { 
    "app_id": "API2020091315292812159", 
    "timestamp": 1600095789, 
    "nonce_str": "035tdGRU3i4yeb38", 
    "name": "aaa", 
    "sign": "36316F06DE635AD51C182C8D5E7495F0" 
}
~~~


### `JSON` 输出格式自定义 

*  如果输出的json格式和需要的格式不一致，可以覆盖绑定默认json响应
*  可以在 `\App\Providers\AppServiceProvider` 内或者其他服务提供者处添加以下代码
~~~
$this->app->bind(\Lake\Admin\Lapi\Contracts\Response::class, YourResponse::class);
~~~
*  使用json响应
~~~
app('lapiJson')->json(boolen $success, int $code, string|null $msg, array|null $data);
~~~


### 模块内 `api` 文件方法注释

*  方法注释
~~~
<?php

namespace Lake\Admin\Lapi\Controller;

class Index
{
    /**
     * 接口方法
     *
     * @title 接口方法标题[必需]
     * @request {"a":"c"}
     * @response {"d":"e"}
     * @description 接口方法描述
     * @order 100
     */
    public function index()
    {
        return json([
            'code' => 0,
            'msg' => 'hello lapi!',
            'data' => 'lapi data',
        ]);
    }
}

~~~


### 模块内使用 

*  `trait` 引用
~~~
use Lake\Admin\Lapi\Traits\Lapi as LapiTrait;

class Index
{
    use LapiTrait;

    // 初始化
    protected function __contruct()
    {
        $this->checkApiSign();
    }
}
~~~

*  `控制器中间件` 使用

在使用中间件的位置引入
~~~
\Lake\Admin\Lapi\Middleware\Lapi::class
~~~
或者
~~~
api.lapi
~~~


### 开源协议

*  该扩展遵循 `Apache2` 开源协议发布，在保留本系统版权（包括版权文件及系统相关标识，相关的标识需在明显位置标示出来）的情况下提供个人及商业免费使用。  
*  使用该项目时，请在明显的位置保留该系统的版权信息。


### 版权

*  该系统所属版权归 deatil(https://github.com/deatil) 所有。
