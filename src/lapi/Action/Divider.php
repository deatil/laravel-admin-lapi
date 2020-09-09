<?php

namespace Lake\Admin\Lapi\Action;

use Encore\Admin\Actions\RowAction;

class Divider extends RowAction
{
    public function render()
    {
        return '<li class="divider"></li>';
    }
}