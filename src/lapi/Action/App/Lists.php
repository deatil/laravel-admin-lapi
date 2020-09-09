<?php   

namespace Lake\Admin\Lapi\Action\App;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;

class Lists extends AbstractTool
{   
    public function render()
    {
        $url = route('admin.lapi.app.index');
        $html = <<<EOF
<div class="btn-group">   
    <a class="btn btn-sm bg-green" href="{$url}" rel="external nofollow" >
        <i class="fa fa-list"></i> 返回列表
    </a> 
</div> 
        
EOF;
        return $html;
    }
} 
