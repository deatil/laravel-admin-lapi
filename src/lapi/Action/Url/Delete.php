<?php

namespace Lake\Admin\Lapi\Action\Url;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Actions\RowAction;

class Delete extends RowAction
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
    
    public function render()
    {
        $url = route("admin.lapi.url.delete");
        $script = <<<EOT
        $('.lake-admin-url-delete').unbind('click').click(function() {
            var id = $(this).data('id');
            swal({
                title: "确认要删除该信息吗？",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确认",
                showLoaderOnConfirm: true,
                cancelButtonText: "取消",
                preConfirm: function() {
                    var data = {
                        id: id,
                        _token: $.admin.token,
                    };
                    
                    $.ajax({
                        method: 'post',
                        url: '$url',
                        data: data,
                        success: function (data) {
                            $.pjax.reload('#pjax-container');
                        }
                    });
                }
            });
        });
EOT;
        Admin::script($script);
        
        return '<a href="javascript:;" class="lake-admin-url-delete" data-id="'.$this->id.'">删除</a>';
    }
}