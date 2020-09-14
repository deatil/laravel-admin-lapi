<?php

namespace Lake\Admin\Lapi\Controller;

use Illuminate\Support\Facades\Validator;

use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

use Lake\Admin\Lapi\Lib\Tree;

use Lake\Admin\Lapi\Model\App as AppModel;
use Lake\Admin\Lapi\Model\Config as ConfigModel;
use Lake\Admin\Lapi\Model\Url as UrlModel;
use Lake\Admin\Lapi\Model\UrlAccess as UrlAccessModel;

use Lake\Admin\Lapi\Action\Divider as DividerAction;
use Lake\Admin\Lapi\Action\App\Delete as AppDeleteAction;
use Lake\Admin\Lapi\Action\App\Detail as AppDetailAction;
use Lake\Admin\Lapi\Action\App\Update as AppUpdateAction;
use Lake\Admin\Lapi\Action\App\Access as AppAccessAction;
use Lake\Admin\Lapi\Action\App\AccessUrl as AppAccessUrlAction;
use Lake\Admin\Lapi\Action\App\Lists as UrlListAction;

/*
 * 授权列表
 *
 * @create 2020-9-5
 * @author deatil
 */
class App
{
    
    /*
     * 首页
     *
     * @create 2020-9-5
     * @author deatil
     */
    public function index(Content $content)
    {
        $grid = new Grid(new AppModel());

        $grid->listorder('排序')
            ->sortable()
            ->style('width:70px');
        $grid->name('名称');
        $grid->app_id('app_id');
        $grid->is_check('权限验证')
            ->display(function ($name) {
                if ($name == 1) {
                    return '<span style="color:green;">启用</span>';
                } else {
                    return '<span style="color:red;">禁用</span>';
                }
            })
            ->style('width:80px');
        $grid->status('状态')
            ->display(function ($name) {
                if ($name == 1) {
                    return '<span class="label label-success">启用</span>';
                } else {
                    return '<span class="label label-danger">禁用</span>';
                }
            })
            ->style('width:60px');
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
        
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
            
            $actions->add(new AppDetailAction($actions->getKey()));
            $actions->add(new AppUpdateAction($actions->getKey()));
            $actions->add(new AppDeleteAction($actions->getKey()));
            
            $actions->add(new DividerAction());
            
            $actions->add(new AppAccessAction($actions->getKey()));
            $actions->add(new AppAccessUrlAction($actions->getKey()));
        });
        
        $grid->disableRowSelector();
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });
        
        return $content
            ->header('授权列表')
            ->description('App授权列表')
            ->body($grid);
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
        $form = new Form(new AppModel());
        
        $form->setAction(route('admin.lapi.app.create'));
        $form->text('name', '名称')->rules('required');
        $form->textarea('description', '描述');
        
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
        ];
        $validator = Validator::make(request()->all(), [
            'name' => 'required|max:255',
        ], $messages);

        if ($validator->fails()) {
            admin_toastr($validator->errors()->first(), 'error');
            return redirect(route('admin.lapi.app.index'));
        }
        
        $post = request()->post();
        
        $appidPre = ConfigModel::getNameValue('api_app_pre');
        if (empty($appidPre)) {
            $appidPre = 'API';
        }
        
        $data = [
            'id' => md5(mt_rand(10000, 99999).time().mt_rand(10000, 99999)),
            'name' => trim($post['name']),
            'app_id' => $appidPre.date('YmdHis').mt_rand(10000, 99999),
            'app_secret' => md5(mt_rand(10000, 99999).time().mt_rand(10000, 99999)),
            'description' => trim($post['description']),
            'allow_origin' => 0,
            'is_check' => 1,
            'status' => (isset($post['status']) && $post['status'] == 1) ? 1 : 0,
            'last_active' => time(),
            'last_ip' => request()->ip(),
            'add_time' => time(),
            'add_ip' => request()->ip(),
        ];
        
        $AppModel = (new AppModel);
        
        foreach ($data as $column => $value) {
            $AppModel->setAttribute($column, $value);
        }

        $AppModel->save();

        admin_toastr('添加成功');
        return redirect(route('admin.lapi.app.index'));
    }

    /**
     * 编辑
     *
     * @create 2020-9-5
     * @author deatil
     *
     */
    public function update($id, Content $content)
    {
        $form = new Form(new AppModel());

        $form->setAction(route('admin.lapi.app.update.run'));
        $form->display('id', 'ID');
        
        $form->text('name', '名称')->rules('required');
        $form->display('app_id', 'Appid')
            ->help('更新状态 app_id 不能被修改');
        $form->display('app_secret', 'Appsecret')
            ->help('更新状态 app_secret 不能被修改');
        $form->radio('update_secret', '更新secret')
            ->options([
                '1' => '启用',
                '0' => '禁用',
            ])
            ->help('当secret泄露的时候更新，通常不需要更新');
        $form->textarea('description', '描述');
        $form->radio('allow_origin', '允许跨域')
            ->options([
                '1' => '启用',
                '0' => '禁用',
            ])
            ->rules('required')
            ->help('跨域跟API用在网页端相关');
        $form->radio('is_check', '签名检测')
            ->options([
                '1' => '启用',
                '0' => '禁用',
            ])
            ->rules('required')
            ->help('是否用于对api接口进行签名检测，默认需要带appid');
        $form->radio('sign_postion', '签名位置')
            ->options([
                'param' => '参数(param)',
                'header' => '头部(header)',
            ])
            ->rules('required')
            ->help('签名字符所放置的位置');
        $form->radio('check_type', '验证类型')
            ->options([
                'MD5' => 'MD5',
                'SHA256' => 'SHA256',
            ])
            ->rules('required')
            ->help('设置检测类型，目前支持 md5 及 sha256 签名');
        $form->text('listorder', '排序');
        $form->radio('status', '状态')->options([
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
            ->header('授权编辑')
            ->description('授权编辑')
            ->body($form);
    }
    
    /**
     * 编辑
     *
     * @create 2020-8-12
     * @author deatil
     */
    public function runUpdate()
    {
        $messages = [
            'email.required' => '名称不能为空',
        ];
        $validator = Validator::make(request()->all(), [
            'name' => 'required|max:255',
        ],  $messages);

        if ($validator->fails()) {
            admin_toastr($validator->errors()->first(), 'error');
            return redirect(route('admin.lapi.app.index'));
        }
        
        $post = request()->post();
        
        $id = $post['id'];
        
        $data = [
            'name' => trim($post['name']),
            'description' => trim($post['description']),
            'allow_origin' => (isset($post['allow_origin']) && $post['allow_origin'] == 1) ? 1 : 0,
            'is_check' => (isset($post['is_check']) && $post['is_check'] == 1) ? 1 : 0,
            'check_type' => trim($post['check_type']),
            'sign_postion' => trim($post['sign_postion']),
            'listorder' => intval($post['listorder']),
            'status' => (isset($post['status']) && $post['status'] == 1) ? 1 : 0,
            'last_active' => time(),
            'last_ip' => request()->ip(),
        ];
        
        if (isset($post['update_secret']) && $post['update_secret'] == 1) {
            $data['app_secret'] = md5(mt_rand(10000, 99999).time().mt_rand(10000, 99999));
        }

        $status = AppModel::where([
            'id' => $id,
        ])->update($data);
        if ($status === false) {
            return admin_error('修改失败', '修改数据失败');
        }
        
        admin_toastr('修改成功');
        return redirect(route('admin.lapi.app.index'));
    }
    
    /**
     * 预览
     *
     * @create 2020-9-5
     * @author deatil
     */
    public function detail($id, Content $content)
    {
        $show = new Show(AppModel::findOrFail($id));
        $show->id('ID');
        $show->name('名称');
        $show->app_id('app_id');
        $show->app_secret('app_secret');
        $show->description('描述');
        $show->allow_origin('允许跨域')->as(function($name) {
            if ($name == 1) {
                return '启用';
            } else {
                return '禁用';
            }
        });
        $show->is_check('权限验证')->as(function($name) {
            if ($name == 1) {
                return '启用';
            } else {
                return '禁用';
            }
        });
        $show->check_type('检测类型');
        $show->sign_postion('签名位置');
        $show->status('状态')->as(function($name) {
            if ($name == 1) {
                return '启用';
            } else {
                return '禁用';
            }
        });
        $show->last_active('最后活动')->as(function($name) {
            return date('Y-m-d H:i:s', $name);
        });
        $show->add_time('添加时间')->as(function($name) {
            return date('Y-m-d H:i:s', $name);
        });
        
        $show->panel()->tools(function ($tools){
            $tools->disableList();
            $tools->disableDelete();
            $tools->disableEdit();
            
            $url = route('admin.lapi.app.index');
            $tools->append('<div class="btn-group pull-right" style="margin-right: 5px">
                <a href="'.$url.'" class="btn btn-sm btn-default" title="返回列表">
                    <i class="fa fa-list"></i><span class="hidden-xs"> 返回列表</span>
                </a>
            </div>');
        });
        
        return $content
            ->header('授权详情')
            ->description('授权详情')
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
        
        $status = AppModel::where('id', $id)->delete();;
        if ($status === false) {
            return admin_error('删除失败', '删除数据失败');
        }
        
        return admin_success('删除成功', '删除数据成功');
    }

    /**
     * 授权
     *
     * @create 2020-9-5
     * @author deatil
     */
    public function access($id, Content $content)
    {
        $form = new Form(new AppModel());
        $form->setAction(route('admin.lapi.app.access.run'));
        $form->display('id', 'ID');
        $form->display('name', '名称');
        
        $Tree = new Tree();
        $urls = UrlModel::select(['parentid','id','title','url','method'])
            ->orderBy('listorder', 'ASC')
            ->orderBy('add_time', 'ASC')
            ->get()
            ->toArray();
        $data = $Tree->withData($urls)->buildArray(0);
        $buildParentUrls = $Tree->buildFormatList($data, 'title');
        $newUrls = [];
        if (!empty($buildParentUrls)) {
            foreach ($buildParentUrls as $url) {
                $newUrls[$url['id']] = $url['spacer'].$url['title'].' ['.$url['method'].'：'.$url['url'].']';
            }
        }
        
        $urlIds = [];
        $accesses = AppModel::find($id)->accesses;
        foreach ($accesses as $access) {
            $urlIds[] = $access['url_id'];
        }
        
        $form->multipleSelect('access', '接口授权')
            ->options($newUrls)
            ->value($urlIds);
        $form->hidden('id');
        $form->edit($id);
        
        $form->disableReset();
        $form->disableViewCheck();
        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        
        $form->tools(function ($tools) {
            $tools->disableList();
            $tools->disableDelete();
            $tools->disableView();
            
            $url = route('admin.lapi.app.index');
            $tools->append('<div class="btn-group pull-right" style="margin-right: 5px">
                <a href="'.$url.'" class="btn btn-sm btn-default" title="返回列表">
                    <i class="fa fa-list"></i><span class="hidden-xs"> 返回列表</span>
                </a>
            </div>');
        }); 

        return $content
            ->header('授权接口')
            ->description('授权接口')
            ->body($form);
    }
    
    /**
     * 访问授权
     *
     * @create 2020-9-8
     * @author deatil
     */
    public function runAccess()
    {
        $id = request()->post('id');
        if (empty($id)) {
            admin_toastr('ID不能为空！', 'error');
            return redirect(route('admin.lapi.app.index'));
        }
        
        $post = request()->post();
        
        $urlIds = $post['access'];
        
        // 删除权限
        UrlAccessModel::where([
            'app_id' => $id,
        ])->delete();
        
        // 有权限就添加
        if (isset($urlIds) && !empty($urlIds)) {
            $urlAccess = [];
            if (!empty($urlIds)) {
                foreach ($urlIds as $urlId) {
                    if (!empty($urlId)) {
                        $urlAccess[] = [
                            'id' => md5(mt_rand(10000, 99999).time().mt_rand(10000, 99999)),
                            'app_id' => $id,
                            'url_id' => $urlId,
                        ];
                    }
                }
            }
            
            $r = (new UrlAccessModel)->insertAll($urlAccess);
        
            if ($r === false) {
                admin_toastr('授权失败', 'error');
                return redirect(route('admin.lapi.app.index'));
            }
        }
        
        admin_toastr('授权成功');
        return redirect(route('admin.lapi.app.index'));
    }
    
    /**
     * 访问授权列表
     *
     * @create 2020-9-5
     * @author deatil
     */
    public function accessUrl($app_id, Content $content)
    {
        if (empty($app_id)) {
            admin_toastr('ID不能为空', 'error');
            return redirect(route('admin.lapi.app.index'));
        }
        
        $grid = new Grid(new UrlAccessModel());

        $grid->column('app.name', '授权名称')
            ->display(function ($name) {
                if (!$name) {
                    return '--';
                }
                
                return $name;
            });
        $grid->column('url.title', '接口名称')
            ->display(function ($name) {
                if (!$name) {
                    return '--';
                }
                
                return $name;
            });
        $grid->column('url.url', '接口地址')
            ->display(function ($name) {
                return $name;
            })
            ->label('info');
        $grid->column('url.method', '请求类型')
            ->display(function ($method) {
                return "<span class=\"badge bg-green\">$method</span>";
            });
        $grid->column('max_request', '最大请求')
            ->editable('text');
        $grid->column('url.status', '状态')
            ->display(function ($name) {
                if ($name == 1) {
                    return '<span class="label label-success">启用</span>';
                } else {
                    return '<span class="label label-danger">禁用</span>';
                }
            })
            ->style('width:80px;');

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->like('url.title');
            $filter->like('app_id');
        });
        
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
        });
        
        $grid->disableRowSelector();
        $grid->disableCreateButton();
        
        $grid->model()->where('app_id', $app_id);
        
        $grid->tools(function ($tools) {
            $tools->append(new UrlListAction());
        }); 
        
        return $content
            ->header('接口列表')
            ->description('接口列表')
            ->body($grid);
    }
    
    /**
     * 访问授权链接设置
     *
     * @create 2020-9-5
     * @author deatil
     */
    public function runAccessUrl($app_id, $id)
    {
        $post = request()->post();
        
        if (empty($app_id)) {
            return response()->json([
                'status' => 0,
                'message' => '参数app_id不能为空！',
            ]);
        }
        
        if (empty($id)) {
            return response()->json([
                'status' => 0,
                'message' => '参数id不能为空！',
            ]);
        }
        
        $name = $post['name'];
        if (empty($name)) {
            return response()->json([
                'status' => 0,
                'message' => '参数name不能为空！',
            ]);
        }
        
        $value = $post['value'];
        
        $rs = UrlAccessModel::where([
            'id' => $id,
            'app_id' => $app_id,
        ])->update([
            $name => $value,
        ]);
        if ($rs === false) {
            return response()->json([
                'status' => 0,
                'message' => '设置失败！',
            ]);
        }
        
        return response()->json([
            'status' => 1,
            'message' => '设置成功！',
        ]);
    }

}
