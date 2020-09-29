<?php

namespace Lake\Admin\Lapi\Controller;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

use Lake\Admin\Lapi\Lib\Tree;
use Lake\Admin\Lapi\Model\Url as UrlModel;
use Lake\Admin\Lapi\Action\Url\Delete as UrlDeleteAction;
use Lake\Admin\Lapi\Action\Url\Detail as UrlDetailAction;
use Lake\Admin\Lapi\Action\Url\Update as UrlUpdateAction;
use Lake\Admin\Lapi\Action\Url\Tree as UrlTreeAction;
use Lake\Admin\Lapi\Action\Url\BatchDestroy as UrlBatchDestroyAction;

/*
 * 接口列表
 *
 * @create 2020-9-7
 * @author deatil
 */
class Url
{
    /**
     * 首页
     *
     * @create 2020-9-7
     * @author deatil
     */
    public function index(Content $content)
    {
        $grid = new Grid(new UrlModel());

        $grid->column('parent.title', '父级')
            ->display(function ($name) {
                if (!$name) {
                    return '<span class="label label-danger">父级</span>';
                }
                
                $name = '<span class="label label-primary">'.$name.'</span>';
                return $name;
            });
        $grid->slug('标识');
        $grid->title('名称');
        $grid->column('路由')
            ->display(function () {
                $methodColors = [
                    'GET'    => 'green',
                    'HEAD'   => 'gray',
                    'POST'   => 'blue',
                    'PUT'    => 'yellow',
                    'DELETE' => 'red',
                    'PATCH'  => 'aqua',
                    'OPTIONS'=> 'light-blue',
                ];
                
                return "<span class=\"label bg-{$methodColors[$this->method]}\">{$this->method}</span><code>{$this->url}</code>";
            });
        $grid->status('状态')
            ->display(function ($name) {
                if ($name == 1) {
                    return '<span class="label label-success">启用</span>';
                } else {
                    return '<span class="label label-danger">禁用</span>';
                }
            })
            ->sortable()
            ->style('width:80px;');
        $grid->add_time('添加时间')
            ->display(function ($name) {
                return date('Y-m-d H:i:s', $name);
            })
            ->style('width:120px')
            ->label('danger');

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->like('name');
            $filter->like('app_id');
        });
        
        $grid->tools(function ($tools) {
            $tools->append(new UrlTreeAction());
        }); 
        
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
            
            $actions->add(new UrlDetailAction($actions->getKey()));
            $actions->add(new UrlUpdateAction($actions->getKey()));
            $actions->add(new UrlDeleteAction($actions->getKey()));
        });
        
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
                
                $batch->add('批量删除', new UrlBatchDestroyAction());
            });
        });
        
        $grid->model()->orderBy('add_time', 'ASC');
        
        return $content
            ->header('接口列表')
            ->description('接口列表')
            ->body($grid);
    }

    /**
     * Create tree.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function tree(Content $content)
    {
        $treeHtml = UrlModel::tree(function ($tree) {
            $tree->disableCreate();
            $tree->branch(function ($branch) {
                return "{$branch['title']}";
            });
        });
            
        return $content->title('接口树结构')
            ->description('接口树结构')
            ->body($treeHtml);
    }
    
    /**
     * Create interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function create(Content $content)
    {
        $form = new Form(new UrlModel());
        
        $form->setAction(route('admin.lapi.url.create'));
        
        $Tree = new Tree();
        $urls = UrlModel::all()->toArray();
        $data = $Tree->withData($urls)->buildArray(0);
        $buildParentUrls = $Tree->buildFormatList($data, 'title');
        $parentUrls = [
            '0' => '作为一级接口',
        ];
        if (!empty($buildParentUrls)) {
            foreach ($buildParentUrls as $url) {
                $parentUrls[$url['id']] = $url['spacer'].$url['title'];
            }
        }
        $form->select('parentid', '父级')
            ->options($parentUrls)
            ->rules('required');
        
        $form->text('title', '名称')
            ->rules('required');
        $form->text('slug', '地址标识')
            ->rules('required');
        $form->textarea('url', '请求地址')
            ->rules('required');
        $form->select('method', '请求类型')
            ->options([
                'GET' => 'GET',
                'POST' => 'POST',
                'PUT' => 'PUT',
                'DELETE' => 'DELETE',
                'PATCH' => 'PATCH',
            ])
            ->value('GET')
            ->rules('required');
        $form->radio('status', '状态')
            ->options([
                '1' => '启用',
                '0' => '禁用',
            ])
            ->value(1);
            
        $form->disableReset();
        $form->disableViewCheck();
        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        
        return $content
            ->header('添加授权')
            ->description('添加授权到列表')
            ->body($form);
    }    
    
    /**
     * 添加
     *
     * @create 2020-9-5
     * @author deatil
     */
    public function runCreate()
    {
        $messages = [
            'email.required' => '名称不能为空',
            'email.max' => '名称的最大长度为：:max',
            'slug.required' => '地址标识不能为空',
            'slug.max' => '地址标识的最大长度为：:max',
            'url.required' => '请求地址不能为空',
            'url.max' => '请求地址的最大长度为：:max',
            'method.required' => '请求类型不能为空',
        ];
        $validator = Validator::make(request()->all(), [
            'title' => 'required|max:200',
            'slug' => 'required|max:50',
            'url' => 'required|max:500',
            'method' => [
                'required',
                Rule::in(['GET', 'POST', 'PUT', 'DELETE', 'PATCH']),
            ],
        ],  $messages);

        if ($validator->fails()) {
            admin_toastr($validator->errors()->first(), 'error');
            return redirect(route('admin.lapi.url.index'));
        }
        
        $post = request()->post();
        
        $data = [
            'id' => md5(mt_rand(10000, 99999).time().mt_rand(10000, 99999)),
            'parentid' => trim($post['parentid']),
            'title' => trim($post['title']),
            'slug' => trim($post['slug']),
            'url' => trim($post['url']),
            'method' => trim($post['method']),
            'status' => (isset($post['status']) && $post['status'] == 1) ? 1 : 0,
            'edit_time' => time(),
            'edit_ip' => request()->ip(),
            'add_time' => time(),
            'add_ip' => request()->ip(),
        ];
        
        $UrlModel = (new UrlModel);
        foreach ($data as $column => $value) {
            $UrlModel->setAttribute($column, $value);
        }

        $UrlModel->save();

        admin_toastr('添加成功');
        return redirect(route('admin.lapi.url.index'));
    }

    /**
     * 编辑
     *
     * @create 2020-9-5
     * @author deatil
     */
    public function update($id, Content $content)
    {
        $form = new Form(new UrlModel());

        $form->setAction(route('admin.lapi.url.update.run'));
        $form->display('id', 'ID');
        
        $urls = UrlModel::all()->toArray();
        $Tree = new Tree();
        $childsId = $Tree->getListChildsId($urls, $id);
        $childsId[] = $id;
        
        $array = [];
        if (!empty($urls)) {
            foreach ($urls as $url) {
                if (in_array($url['id'], $childsId)) {
                    continue;
                }
                
                $array[] = $url;
            }
        }
        $data = $Tree->withData($array)->buildArray(0);
        $buildParentUrls = $Tree->buildFormatList($data, 'title');
        
        $parentUrls = [
            '0' => '作为一级接口',
        ];
        if (!empty($buildParentUrls)) {
            foreach ($buildParentUrls as $url) {
                $parentUrls[$url['id']] = $url['spacer'].$url['title'];
            }
        }
        
        $form->select('parentid', '父级')
            ->options($parentUrls)
            ->rules('required');
        
        $form->text('title', '名称')
            ->rules('required');
        $form->text('slug', '地址标识')
            ->rules('required');
        $form->textarea('url', '请求地址')
            ->rules('required');
        $form->select('method', '请求类型')
            ->options([
                'GET' => 'GET',
                'POST' => 'POST',
                'PUT' => 'PUT',
                'DELETE' => 'DELETE',
                'PATCH' => 'PATCH',
            ])
            ->rules('required');
        $form->textarea('request', '请求字段')
            ->help('接口的请求字段');
        $form->textarea('response', '响应字段')
            ->help('接口的响应字段');
        $form->textarea('description', '描述')
            ->help('接口的描述');
        $form->text('listorder', '排序')
            ->rules('required');
        $form->radio('status', '状态')
            ->options([
                '1' => '启用',
                '0' => '禁用',
            ]);
        $form->hidden('id');
        
        $form->disableReset();
        $form->disableViewCheck();
        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        
        $form->edit($id);

        return $content
            ->header('接口编辑')
            ->description('接口编辑')
            ->body($form);
    }
    
    /**
     * 编辑
     *
     * @create 2020-9-5
     * @author deatil
     */
    public function runUpdate()
    {
        $messages = [
            'email.required' => '名称不能为空',
            'email.max' => '名称的最大长度为：:max',
            'slug.required' => '地址标识不能为空',
            'slug.max' => '地址标识的最大长度为：:max',
            'url.required' => '请求地址不能为空',
            'url.max' => '请求地址的最大长度为：:max',
            'method.required' => '请求类型不能为空',
            'listorder.required' => '排序不能为空',
            'listorder.min' => '排序的最小长度为：:min',
            'listorder.max' => '排序的最大长度为：:max',
        ];
        $validator = Validator::make(request()->all(), [
            'title' => 'required|max:200',
            'slug' => 'required|max:50',
            'url' => 'required|max:500',
            'method' => [
                'required',
                Rule::in(['GET', 'POST', 'PUT', 'DELETE', 'PATCH']),
            ],
            'listorder' => 'required|min:1|max:5',
        ], $messages);

        if ($validator->fails()) {
            admin_toastr($validator->errors()->first(), 'error');
            return redirect(route('admin.lapi.url.index'));
        }
        
        $post = request()->post();
        
        $id = $post['id'];
        
        $data = [
            'parentid' => trim($post['parentid']),
            'title' => trim($post['title']),
            'slug' => trim($post['slug']),
            'url' => trim($post['url']),
            'method' => trim($post['method']),
            'request' => trim($post['request']),
            'response' => trim($post['response']),
            'description' => trim($post['description']),
            'listorder' => intval($post['listorder']),
            'status' => (isset($post['status']) && $post['status'] == 1) ? 1 : 0,
            'edit_time' => time(),
            'edit_ip' => request()->ip(),
        ];

        $status = UrlModel::where([
            'id' => $id,
        ])->update($data);
        if ($status === false) {
            return admin_error('修改失败', '修改数据失败');
        }
        
        admin_toastr('修改成功');
        return redirect(route('admin.lapi.url.index'));
    }
    
    /**
     * 预览
     *
     * @create 2020-9-5
     * @author deatil
     */
    public function detail($id, Content $content)
    {
        $show = new Show(UrlModel::findOrFail($id));
        $show->id('ID');
        $show->title('名称');
        $show->slug('链接标识');
        $show->url('请求链接');
        $show->method('请求方式');
        $show->request('请求字段');
        $show->response('响应字段');
        $show->description('描述');
        $show->listorder('排序');
        $show->status('状态')->as(function($name) {
            if ($name == 1) {
                return '启用';
            } else {
                return '禁用';
            }
        });
        $show->add_time('添加时间')->as(function($name) {
            return date('Y-m-d H:i:s', $name);
        });
        
        $show->panel()->tools(function ($tools){
            $tools->disableList();
            $tools->disableDelete();
            $tools->disableEdit();
            
            $url = route('admin.lapi.url.index');
            $tools->append('<div class="btn-group pull-right" style="margin-right: 5px">
                <a href="'.$url.'" class="btn btn-sm btn-default" title="返回列表">
                    <i class="fa fa-list"></i><span class="hidden-xs"> 返回列表</span>
                </a>
            </div>'
            );
        });
        
        return $content
            ->header('接口详情')
            ->description('接口详情')
            ->body($show);
    }

    /**
     * 删除
     *
     * @create 2020-9-5
     * @author deatil
     */
    public function runDelete()
    {
        $id = request()->post('id');
        if (empty($id)) {
            return admin_error('删除失败', '删除数据失败');
        }
        
        $count = UrlModel::where('parentid', $id)
            ->count();
        if ($count > 0) {
            return admin_error('删除失败', '该父级还有子级数据，请删除子级后重试');
        }
        
        $status = UrlModel::where('id', $id)->delete();
        if ($status === false) {
            return admin_error('删除失败', '删除数据失败');
        }
        
        return admin_success('删除成功', '删除数据成功');
    }

    /**
     * 批量删除
     *
     * @create 2020-9-14
     * @author deatil
     */
    public function runDestroy()
    {
        $ids = request()->post('ids');
        if (empty($ids)) {
            return admin_error('批量删除失败', '删除数据失败');
        }
        
        $ids = explode(',', $ids);
        
        foreach ($ids as $id) {
            $count = UrlModel::where('parentid', $id)
                ->count();
            if ($count > 0) {
                return admin_error('批量删除失败', '该父级还有子级数据，请删除子级后重试');
            }
        }
        
        $status = UrlModel::whereIn('id', $ids)->delete();
        if ($status === false) {
            return admin_error('批量删除失败', '批量删除数据失败');
        }
        
        return admin_success('批量删除成功', '批量删除数据成功');
    }

}
