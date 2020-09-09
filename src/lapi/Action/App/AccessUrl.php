<?php

namespace Lake\Admin\Lapi\Action\App;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Actions\RowAction;

class AccessUrl extends RowAction
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
    
    public function render()
    {
        return '<a href="'.route('admin.lapi.app.access.url', ['id' => $this->id]).'" class="lake-admin-app-access-url">接口列表</a>';
    }
}