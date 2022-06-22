## Api授权管理


### 项目介绍

*  基于 `laravel-admin` 后台管理框架的api管理系统模块扩展
*  签名算法包括md5和Sha256摘要算法。
*  扩展实现了api的签名验证，请求日志记录，每个appid单独授权api接口等等。


### 签名算法

*  第一步，设所有发送或者接收到的数据为集合M，将集合M内非空参数值的参数按照参数名ASCII码从小到大排序（字典序），使用URL键值对的格式（即key1=value1&key2=value2…）拼接成字符串stringA。 
*  第二步，在stringA最后拼接上key（即key=keyValue）得到stringSignTemp字符串，并对stringSignTemp进行MD5运算，再将得到的字符串所有字符转换为大写，得到sign值signValue。
*  特别注意以下重要规则： 
~~~
◆ 参数名ASCII码从小到大排序（字典序）；
◆ 如果参数的值为空不参与签名；
◆ 参数名区分大小写；
◆ 验证调用返回或服务器主动通知签名时，传送的sign参数不参与签名，将生成的签名与该sign值作校验；
◆ 接口可能增加字段，验证签名时必须支持增加的扩展字段 
~~~
*  签名数据注意：默认签名数据包括 `post` 及 `get` 数据集合，即签名数据为 `request()->all()`。


### 使用方法 

*  安装
~~~
composer require lake/laravel-admin-lapi

php artisan admin:import lapi
~~~

*  导入路由信息，解析api控制器注释
~~~
php artisan lapi:import-route
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
app('lapi.json')->json(boolen $success, int $code, string|null $msg, array|null $data);
~~~


### 模块内 `api` 文件方法注释

*  方法注释
~~~
<?php

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
        // $this->withData($data); // 自定义签名数据，非必须
        
        $this->checkApi();
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
注意：如需自定义签名数据，可以在使用中间件之前添加 `app('lapi.check')->withData($data);`

### 开源协议

*  `laravel-admin-lapi` 遵循 `Apache2` 开源协议发布，在保留本扩展版权的情况下提供个人及商业免费使用。 


### 版权

*  该系统所属版权归 deatil(https://github.com/deatil) 所有。
