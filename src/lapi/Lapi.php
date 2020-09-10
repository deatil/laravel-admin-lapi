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
            $router->group([
                'prefix'     => 'lapi',
            ], function ($router) {
                $router->namespace('\\Lake\\Admin\\Lapi\\Controller')->group(function ($router) {
                    $router->get('setting', 'Setting@index')->name('admin.lapi.setting');
                    $router->post('setting', 'Setting@runIndex')->name('admin.lapi.setting');
                    
                    $router->get('app', 'App@index')->name('admin.lapi.app.index');
                    $router->get('app/create', 'App@create')->name('admin.lapi.app.create');
                    $router->post('app/create', 'App@runCreate')->name('admin.lapi.app.create');
                    $router->get('app/{id}/update', 'App@update')->name('admin.lapi.app.update');
                    $router->put('app/update', 'App@runUpdate')->name('admin.lapi.app.update.run');
                    $router->post('app/delete', 'App@runDelete')->name('admin.lapi.app.delete');
                    $router->get('app/{id}', 'App@detail')->name('admin.lapi.app.detail');
                    $router->get('app/{id}/access', 'App@access')->name('admin.lapi.app.access');
                    $router->put('app/access', 'App@runAccess')->name('admin.lapi.app.access.run');
                    $router->get('app/{id}/access/url', 'App@accessUrl')->name('admin.lapi.app.access.url');
                    $router->put('app/{app_id}/access/url/{id}', 'App@runAccessUrl')->name('admin.lapi.app.access.url.run');
                    
                    $router->get('url', 'Url@index')->name('admin.lapi.url.index');
                    $router->get('url/tree', 'Url@tree')->name('admin.lapi.url.tree');
                    $router->get('url/create', 'Url@create')->name('admin.lapi.url.create');
                    $router->post('url/create', 'Url@runCreate')->name('admin.lapi.url.create');
                    $router->get('url/{id}', 'Url@detail')->name('admin.lapi.url.detail');
                    $router->get('url/{id}/update', 'Url@update')->name('admin.lapi.url.update');
                    $router->put('url/update', 'Url@runUpdate')->name('admin.lapi.url.update.run');
                    $router->post('url/delete', 'Url@runDelete')->name('admin.lapi.url.delete');
                    
                    $router->get('log', 'Log@index')->name('admin.lapi.log.index');
                    $router->get('log/{id}', 'Log@detail')->name('admin.lapi.log.detail');
                    $router->post('log/clear', 'Log@runClear')->name('admin.lapi.log.clear');
                });
            });
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
                'uri'       => 'setting',
            ],
            [
                'title'     => '授权列表',
                'icon'      => 'fa-align-justify',
                'uri'       => 'app',
            ],
            [
                'title'     => '接口列表',
                'icon'      => 'fa-clipboard',
                'uri'       => 'url',
            ],
            [
                'title'     => '接口日志',
                'icon'      => 'fa-font-awesome',
                'uri'       => 'log',
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

        parent::createPermission('APP授权', 'ext.lapi', '*');
    }
}
