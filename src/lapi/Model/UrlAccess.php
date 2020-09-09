<?php

namespace Lake\Admin\Lapi\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

/*
 * UrlAccess 模型
 *
 * @create 2020-9-5
 * @author deatil
 */
class UrlAccess extends Model
{
    protected $table = 'lapi_url_access';
    protected $keyType = 'string';
    protected $pk = 'id';
    
    public $timestamps = false;
    
    public function insertAll(Array $data)
    {
        $rs = DB::table($this->getTable())->insert($data);
        return $rs;
    }
    
    /**
     * url
     */
    public function url()
    {
        return $this->hasOne(Url::class, 'id', 'url_id');
    }
    
    /**
     * app
     */
    public function app()
    {
        return $this->hasOne(App::class, 'id', 'app_id');
    }
}
