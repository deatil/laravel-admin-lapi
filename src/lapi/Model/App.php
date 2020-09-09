<?php

namespace Lake\Admin\Lapi\Model;

use Illuminate\Database\Eloquent\Model;

/*
 * App模型
 *
 * @create 2020-9-5
 * @author deatil
 */
class App extends Model
{
    protected $table = 'lapi_app';
    protected $keyType = 'string';
    protected $pk = 'id';
    
    public $incrementing = false;
    public $timestamps = false;
    
    /**
     * app
     */
    public function accesses()
    {
        return $this->hasMany(UrlAccess::class, 'app_id', 'id');
    }
}
