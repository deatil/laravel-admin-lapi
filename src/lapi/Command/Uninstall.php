<?php

namespace Lake\Admin\Lapi\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use Encore\Admin\Admin;
use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Auth\Database\Permission;

/**
 * 卸载
 *
 * php artisan lapi:uninstall
 */
class Uninstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lapi:uninstall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'lapi 扩展卸载';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 执行sql
        $sqlFile = __DIR__.'/../../resources/sql/uninstall.sql';
        $dbPrefix = DB::getConfig('prefix');
        $sqls = file_get_contents($sqlFile);
        $sqls = str_replace('pre__', $dbPrefix, $sqls);
        DB::unprepared($sqls);
        
        $msg = Menu::where('uri', 'like', 'lapi%')
                ->delete();
        $msg = Permission::where('slug', 'like', 'ext.lapi')
                ->delete();
            
        $this->info('lapi 扩展卸载成功');
    }
}
