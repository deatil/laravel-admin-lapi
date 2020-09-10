<?php

namespace Lake\Admin\Lapi\Controller;

use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Actions\RowAction;

use Lake\Admin\Lapi\Action\AppLog\Clear as ClearAction;
use Lake\Admin\Lapi\Action\AppLog\Detail as DetailAction;
use Lake\Admin\Lapi\Model\AppLog as AppLogModel;

/*
 * app访问记录
 *
 * @create 2020-9-8
 * @author deatil
 */
class Log
{
    
    /**
     * 列表
     *
     * @create 2020-9-8
     * @author deatil
     */
    public function index(Content $content)
    {
        $grid = new Grid(new AppLogModel());

        $grid->column('app.name', '授权名称')
            ->display(function ($name) {
                if (!$name) {
                    return '--';
                }
                
                return $name;
            });
        $grid->api('接口标识')->label('info');
        $grid->column('请求链接')
            ->display(function () {
                return "<span class=\"badge bg-green\">{$this->method}</span><code>{$this->url}</code>";
            })
            ->style("max-width:200px;word-break:break-all;");
        $grid->add_time('添加时间')
            ->display(function ($name) {
                return date('Y-m-d H:i:s', $name);
            })
            ->style('width:120px')
            ->label('danger');

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->like('app_id');
            $filter->like('api');
            $filter->like('url');
        });
        
        $grid->disableRowSelector();
        $grid->disableCreateButton();
        
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
            
            $actions->add(new DetailAction($actions->getKey()));
        });
        
        $grid->tools(function ($tools) {
            $tools->append(new ClearAction());
        }); 
        
        $grid->model()->orderBy('add_time', 'DESC');
        
        return $content
            ->header('接口日志')
            ->description('接口日志列表')
            ->body($grid);
    }
    
    /**
     * 详情
     *
     * @create 2020-9-8
     * @author deatil
     */
    public function detail($id = '', Content $content)
    {
        $show = new Show(AppLogModel::findOrFail($id));
        $show->id('ID');
        $show->app_id('app_id');
        $show->app('授权名称')->as(function ($app) {
            return $app['name'];
        });
        $show->api('请求接口');
        $show->url('请求链接');
        $show->method('请求方式');
        $show->useragent('请求来源');
        $show->header('请求头信息');
        $show->payload('请求内容');
        $show->content('请求原始内容');
        $show->cookie('请求Cookie');
        $show->add_time('添加时间')->as(function($name) {
            return date('Y-m-d H:i:s', $name);
        });
        
        $show->panel()->tools(function ($tools){
            $tools->disableList();
            $tools->disableDelete();
            $tools->disableEdit();
            
            $url = route('admin.lapi.log.index');
            $tools->append('<div class="btn-group pull-right" style="margin-right: 5px">
                <a href="'.$url.'" class="btn btn-sm btn-default" title="返回列表">
                    <i class="fa fa-list"></i><span class="hidden-xs"> 返回列表</span>
                </a>
            </div>'
            );
        });
        
        Admin::style(".form-group .box-body{word-break:break-all;}");
        
        return $content
            ->header('接口详情')
            ->description('接口详情')
            ->body($show);
    }

    /**
     * 清除记录
     *
     * @create 2020-9-5
     * @author deatil
     */
    public function runClear()
    {
        // 删除二十天前数据
        $status = AppLogModel::where('add_time', '<=', time() - 60 * 60 * 24 * 20)->delete();
        if ($status === false) {
            return admin_error('清除失败', '清除数据失败');
        }
        
        return admin_success('清除成功', '清除数据成功');
    }
    
}
