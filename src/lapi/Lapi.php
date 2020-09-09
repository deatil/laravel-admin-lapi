<?php

namespace Lake\Admin\Lapi;

use Illuminate\Support\Facades\DB;

use Encore\Admin\Admin;
use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Extension;

class Lapi extends Extension
{
    /**
     * Bootstrap this package.
     *
     * @return void
     */
    public static function boot()
    {
        static::registerRoutes();

        Admin::extend('lapi', __CLASS__);
    }

    /**
     * Register routes for laravel-admin.
     *
     * @return void
     */
    public static function registerRoutes()
    {
        parent::routes(function ($router) {
            /* @var \Illuminate\Routing\Router $router */
            $router->get('lapi/setting', 'Lake\Admin\Lapi\Controller\SettingController@index')->name('admin.lapi.setting');
            $router->post('lapi/setting', 'Lake\Admin\Lapi\Controller\SettingController@runIndex')->name('admin.lapi.setting');
            
            $router->get('lapi/app', 'Lake\Admin\Lapi\Controller\AppController@index')->name('admin.lapi.app.index');
            $router->get('lapi/app/create', 'Lake\Admin\Lapi\Controller\AppController@create')->name('admin.lapi.app.create');
            $router->post('lapi/app/create', 'Lake\Admin\Lapi\Controller\AppController@runCreate')->name('admin.lapi.app.create');
            $router->get('lapi/app/{id}/update', 'Lake\Admin\Lapi\Controller\AppController@update')->name('admin.lapi.app.update');
            $router->put('lapi/app/update', 'Lake\Admin\Lapi\Controller\AppController@runUpdate')->name('admin.lapi.app.update.run');
            $router->post('lapi/app/delete', 'Lake\Admin\Lapi\Controller\AppController@runDelete')->name('admin.lapi.app.delete');
            $router->get('lapi/app/{id}', 'Lake\Admin\Lapi\Controller\AppController@detail')->name('admin.lapi.app.detail');
            $router->get('lapi/app/{id}/access', 'Lake\Admin\Lapi\Controller\AppController@access')->name('admin.lapi.app.access');
            $router->put('lapi/app/access', 'Lake\Admin\Lapi\Controller\AppController@runAccess')->name('admin.lapi.app.access.run');
            $router->get('lapi/app/{id}/access/url', 'Lake\Admin\Lapi\Controller\AppController@accessUrl')->name('admin.lapi.app.access.url');
            $router->put('lapi/app/{app_id}/access/url/{id}', 'Lake\Admin\Lapi\Controller\AppController@runAccessUrl')->name('admin.lapi.app.access.url.run');
            
            $router->get('lapi/url', 'Lake\Admin\Lapi\Controller\UrlController@index')->name('admin.lapi.url.index');
            $router->get('lapi/url/tree', 'Lake\Admin\Lapi\Controller\UrlController@tree')->name('admin.lapi.url.tree');
            $router->get('lapi/url/create', 'Lake\Admin\Lapi\Controller\UrlController@create')->name('admin.lapi.url.create');
            $router->post('lapi/url/create', 'Lake\Admin\Lapi\Controller\UrlController@runCreate')->name('admin.lapi.url.create');
            $router->get('lapi/url/{id}', 'Lake\Admin\Lapi\Controller\UrlController@detail')->name('admin.lapi.url.detail');
            $router->get('lapi/url/{id}/update', 'Lake\Admin\Lapi\Controller\UrlController@update')->name('admin.lapi.url.update');
            $router->put('lapi/url/update', 'Lake\Admin\Lapi\Controller\UrlController@runUpdate')->name('admin.lapi.url.update.run');
            $router->post('lapi/url/delete', 'Lake\Admin\Lapi\Controller\UrlController@runDelete')->name('admin.lapi.url.delete');
            
            $router->get('lapi/log', 'Lake\Admin\Lapi\Controller\LogController@index')->name('admin.lapi.log.index');
            $router->get('lapi/log/{id}', 'Lake\Admin\Lapi\Controller\LogController@detail')->name('admin.lapi.log.detail');
            $router->post('lapi/log/clear', 'Lake\Admin\Lapi\Controller\LogController@runClear')->name('admin.lapi.log.clear');
        });
    }

    public static function import()
    {
        $lastOrder = Menu::max('order');

        $root = [
            'parent_id' => 0,
            'order'     => $lastOrder++,
            'title'     => 'APP授权',
            'icon'      => 'fa-th-list',
            'uri'       => '',
        ];

        $root = Menu::create($root);

        $menus = [
            [
                'title'     => '授权设置',
                'icon'      => 'fa-cog',
                'uri'       => 'lapi/setting',
            ],
            [
                'title'     => '授权列表',
                'icon'      => 'fa-align-justify',
                'uri'       => 'lapi/app',
            ],
            [
                'title'     => '接口列表',
                'icon'      => 'fa-clipboard',
                'uri'       => 'lapi/url',
            ],
            [
                'title'     => '接口日志',
                'icon'      => 'fa-font-awesome',
                'uri'       => 'lapi/log',
            ],
        ];

        foreach ($menus as $menu) {
            $menu['parent_id'] = $root->id;
            $menu['order'] = $lastOrder++;

            Menu::create($menu);
        }
        
        // 执行数据库
        $installSqlFile = __DIR__.'/../resources/sql/install.sql';
        $dbPrefix = DB::getConfig('prefix');
        $sqls = file_get_contents($installSqlFile);
        $sqls = str_replace('pre__', $dbPrefix, $sqls);
        DB::unprepared($sqls);

        parent::createPermission('APP授权', 'ext.lapi', 'lapi/*');
    }
}
