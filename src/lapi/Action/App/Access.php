<?php

namespace Lake\Admin\Lapi\Action\App;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Actions\RowAction;

class Access extends RowAction
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
    
    public function render()
    {
        return '<a href="'.route('admin.lapi.app.access', ['id' => $this->id]).'" class="lake-admin-app-deatil">接口授权</a>';
    }
}