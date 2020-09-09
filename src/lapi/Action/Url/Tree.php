<?php   

namespace Lake\Admin\Lapi\Action\Url;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;

class Tree extends AbstractTool
{   
    public function render()
    {
        $url = route('admin.lapi.url.tree');
        $html = <<<EOF
<div class="btn-group">   
    <a class="btn btn-sm bg-green" href="{$url}" rel="external nofollow" >
        查看树结构
    </a> 
</div> 
        
EOF;
        return $html;
    }
} 
