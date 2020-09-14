<?php

namespace Lake\Admin\Lapi\Action\Url;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid\Tools\BatchAction;

class BatchDestroy extends BatchAction
{
    public function script()
    {
        $url = route("admin.lapi.url.destroy");
        $script = <<<EOT
        $('{$this->getElementClass()}').unbind('click').click(function() {
            var id = $(this).data('id');
            swal({
                title: "确认要删除吗？",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确认",
                showLoaderOnConfirm: true,
                cancelButtonText: "取消",
                preConfirm: function() {
                    var data = {
                        ids: $.admin.grid.selected().join(),
                        _token: $.admin.token,
                    };
                    
                    $.ajax({
                        method: 'post',
                        url: '$url',
                        data: data,
                        success: function (data) {
                            $.pjax.reload('#pjax-container');
                            toastr.success('操作成功');
                        }
                    });
                }
            });
        });
EOT;
        
        return $script;
    }
}