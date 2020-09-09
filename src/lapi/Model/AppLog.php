<?php

namespace Lake\Admin\Lapi\Model;

use Illuminate\Database\Eloquent\Model;

/*
 * AppLog 模型
 *
 * @create 2020-9-5
 * @author deatil
 */
class AppLog extends Model
{
    protected $table = 'lapi_app_log';
    protected $keyType = 'string';
    protected $pk = 'id';
    
    public $incrementing = false;
    public $timestamps = false;
    
    /**
     * app
     */
    public function app()
    {
        return $this->hasOne(App::class, 'app_id', 'app_id');
    }
}
