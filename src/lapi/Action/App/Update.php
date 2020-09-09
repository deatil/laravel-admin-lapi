<?php

namespace Lake\Admin\Lapi\Action\App;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Actions\RowAction;

class Update extends RowAction
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
    
    public function render()
    {
        return '<a href="'.route('admin.lapi.app.update', ['id' => $this->id]).'" class="lake-admin-app-update">编辑</a>';
    }
}