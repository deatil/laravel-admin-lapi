<?php

namespace Lake\Admin\Lapi\Action\AppLog;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Actions\RowAction;

class Detail extends RowAction
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
    
    public function render()
    {
        return '<a href="'.route('admin.lapi.log.detail', ['id' => $this->id]).'" class="lake-admin-log-deatil">预览</a>';
    }
}