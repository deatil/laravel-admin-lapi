<?php

namespace Lake\Admin\Lapi\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/*
 * LapiConfig 模型
 *
 * @create 2020-8-14
 * @author deatil
 */
class Config extends Model
{
    protected $table = 'lapi_config';
    protected $pk = 'id';
    
    public $timestamps = false;
    
    /*
     * 获取列表
     *
     * @create 2020-8-15
     * @author deatil
     */
    public static function getList()
    {
        $setting = Cache::get("lapi_config");
        if (!$setting) {
            $config = self::all();
            
            $setting = [];
            if (!empty($config)) {
                foreach ($config as $val) {
                    $setting[$val['name']] = $val['value'];
                }
            }
            
            Cache::put("lapi_config", $setting);
        }
        
        return $setting;
    }
    
    /*
     * 获取数据
     *
     * @create 2020-8-15
     * @author deatil
     */
    public static function getNameValue($name)
    {
        $value = self::where([
            'name' => $name,
        ])->value('value');
        
        return $value;
    }
    
}
