<?php

namespace Lake\Admin\Lapi\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use Encore\Admin\Auth\Database\Menu;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lapi:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'lapi 扩展安装';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $lastOrder = Menu::max('order');

        $root = [
            'parent_id' => 0,
            'order'     => $lastOrder++,
            'title'     => 'APP授权',
            'icon'      => 'fa-th-list',
            'uri'       => 'lapi',
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
        $installSqlFile = __DIR__.'/../../resources/sql/install.sql';
        $dbPrefix = DB::getConfig('prefix');
        $sqls = file_get_contents($installSqlFile);
        $sqls = str_replace('pre__', $dbPrefix, $sqls);
        DB::unprepared($sqls);
            
        $this->info('lapi 扩展安装成功');
    }
}
