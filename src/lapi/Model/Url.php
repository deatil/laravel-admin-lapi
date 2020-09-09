<?php

namespace Lake\Admin\Lapi\Model;

use Illuminate\Database\Eloquent\Model;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;

/*
 * Url 模型
 *
 * @create 2020-9-5
 * @author deatil
 */
class Url extends Model
{
    use ModelTree,AdminBuilder;
    
    protected $table = 'lapi_url';
    protected $keyType = 'string';
    protected $pk = 'id';
    
    public $incrementing = false;
    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setParentColumn('parentid');
        $this->setOrderColumn('listorder');
        $this->setTitleColumn('title');
    }
    
    /**
     * 父级
     */
    public function parent()
    {
        return $this->hasOne(Url::class, 'id', 'parentid');
    }

    /*
     * 获取数据
     *
     * @create 2020-8-18
     * @author deatil
     */
    public static function getDataByUrl($url = '')
    {
        if (empty($url)) {
            return [];
        }
        
        return self::where([
            'url' => $url,
        ])->find();
    }
    
    /*
     * 添加数据
     *
     * @create 2020-8-18
     * @author deatil
     */
    public static function insertUrl($data = [])
    {
        if (empty($data)) {
            return false;
        }
        
        $newData = array_merge([
            'id' => md5(mt_rand(10000, 99999).time().mt_rand(10000, 99999)),
            'edit_time' => time(),
            'edit_ip' => request()->ip(),
            'add_time' => time(),
            'add_ip' => request()->ip(),
        ], $data);
        $newInfo = (new self)->create($newData);
        $newId = $newInfo->id;
        return $newId;
    }
}
