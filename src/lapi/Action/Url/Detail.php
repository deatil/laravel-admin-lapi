<?php

namespace Lake\Admin\Lapi\Action\Url;

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
        return '<a href="'.route('admin.lapi.url.detail', ['id' => $this->id]).'" class="lake-admin-url-deatil">预览</a>';
    }
}