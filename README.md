## api管理系统


### 项目介绍

*  基于 `laravel-admin` 后台管理框架的api管理系统模块插件
*  `签名算法` 借鉴于微信支付，具体算法可以查看微信支付文档


### 使用方法 

~~~
composer require lake/laravel-admin-lapi

$ php artisan admin:import lapi
~~~


### 请求示例 
*  GET: /api/bbb?app_id=API2020090715460852331&name=aakae&nonce_str=XWlID5b4pqArIEnU&sign=45A5CF2CD9C0321BBD93E1B170CB0B8E&timestamp=1599666165


### 模块内 `api` 文件方法设置

*  方法设置
~~~
<?php

namespace Lake\Admin\Lapi\Controller;

/**
 * @title 接口标题[必需]
 * @description 接口描述
 */
class Index
{
    /**
     * 接口方法
     *
     * @title 接口方法标题[必需]
     * @slug aa.bb[必需，链接标识]
     * @method GET[必需]
     * @request {"a":"c"}
     * @response {"d":"e"}
     * @description 接口方法描述
     * @listorder 100
     * @status 1
     */
    public function index()
    {
        return json([
            'code' => 0,
            'msg' => 'hello world!',
            'data' => 'api data',
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


### 开源协议

*  该插件遵循 `Apache2` 开源协议发布，在保留本系统版权（包括版权文件及系统相关标识，相关的标识需在明显位置标示出来）的情况下提供个人及商业免费使用。  
*  使用该项目时，请在明显的位置保留该系统的版权标识（标识包括：lake，lake-admin及该系统所属logo），并不得修改后台版权信息。


### 版权

*  该系统所属版权归 deatil(https://github.com/deatil) 所有。
