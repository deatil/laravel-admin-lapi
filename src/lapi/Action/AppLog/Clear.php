<?php   

namespace Lake\Admin\Lapi\Action\AppLog;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;

class Clear extends AbstractTool
{   
    public function render()
    {
        $script = <<<EOT
        $('.js-lapi-log-clear').unbind('click').click(function() {
            var url = $(this).attr('href');
            swal({
                title: "确认要清除信息记录吗？",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确认",
                showLoaderOnConfirm: true,
                cancelButtonText: "取消",
                preConfirm: function() {
                    var data = {
                        _token: $.admin.token,
                    };
                    
                    $.ajax({
                        method: 'post',
                        url: url,
                        data: data,
                        success: function (data) {
                            $.pjax.reload('#pjax-container');
                        }
                    });
                }
            });
            
            return false;
        });
EOT;
        Admin::script($script);
        
        $url = route("admin.lapi.log.clear");   
        $icon = "fa-trash";
        $text = "清除一个月前记录";   
        $btnType = "btn-danger";   
        
        $html = <<<EOF
<div class="btn">   
    <a class="btn btn-sm {$btnType} pull-right lapi-log-clear js-lapi-log-clear" href="{$url}" rel="external nofollow" >
        <i class="fa {$icon}"></i> {$text}
    </a> 
</div> 
        
EOF;
        return $html;
    }
} 
