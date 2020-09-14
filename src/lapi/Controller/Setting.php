<?php

namespace Lake\Admin\Lapi\Controller;

use Illuminate\Support\Facades\Cache;
use Illuminate\Routing\Controller;

use Encore\Admin\Layout\Content;
use Encore\Admin\Facades\Admin;

use Lake\Admin\Lapi\Model\Config as ConfigModel;

/**
 * 设置
 *
 * @create 2020-9-5
 * @author deatil
 */
class Setting
{
    /**
     * 设置
     *
     * @create 2020-9-5
     * @author deatil
     */
    public function index(Content $content)
    {
        $config = ConfigModel::orderBy('name', 'desc')
            ->get();
        
        $setting = [];
        if (!empty($config)) {
            foreach ($config as $val) {
                $setting[$val['name']] = $val['value'];
            }
        }
        
        $script = <<<EOT
        $('.api_close').iCheck({radioClass:'iradio_minimal-blue'}); 
        $('.open_putlog').iCheck({radioClass:'iradio_minimal-blue'}); 
EOT;
        
        Admin::script($script);
            
        return $content
            ->header('授权设置')
            ->description('授权设置')
            ->body(view('lapi::setting.index', [
                'setting' => $setting,
            ])
            ->render());
    }
    
    /**
     * 设置
     *
     * @create 2020-9-5
     * @author deatil
     */
    public function runIndex()
    {
        $data = request()->post();

        $updateData = [
            'api_close' => $data['api_close'],
            'api_close_tip' => $data['api_close_tip'],
            'api_app_pre' => $data['api_app_pre'],
            'open_putlog' => $data['open_putlog'],
        ];
        
        foreach ($updateData as $key => $value) {
            ConfigModel::where([
                'name' => $key,
            ])->update([
                'value' => $value,
            ]);
        }
        
        Cache::forget("lapi_config");
        
        admin_toastr('设置成功');
        return back();
    }
}
