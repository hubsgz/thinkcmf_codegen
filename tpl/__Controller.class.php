<?php
/**
 * {$business_name}管理
 */
namespace {$module_name}\Controller;

use Common\Controller\AdminbaseController;

class Admin{$controllername}Controller extends AdminbaseController
{
    public $model = null;
    
    public function __construct()
    {
        parent::__construct();
        $this->model = D('{$classname}'); 
    }    
    
    /**
     * 列表
     */
    public function index()
    {
        $condition = array();
        
        //总数
        $count = $this->model->where($condition)->count();
        
        //分页
        $pagesize = 20;
        $page = $this->page($count, $pagesize);
        
        //列表
        $list = $this->model->where($condition)->page($page->Current_page, $pagesize)->select();
        $this->assign("page", $page->show('Admin'));
        $this->assign('list', $list);
        
        $this->display();
    }
    
    /**
     * 添加和修改
     */
    public function add()
    {
        $model = $this->model;
        
        if (IS_POST) {
            //print_r($_POST);
            if ($data = $model->create()) {         
                if ($data['{$prikey}'] > 0) {
                    $ret = $model->where(array('{$prikey}'=>$data['{$prikey}']))->save();
                } else {
                    $ret = $model->add();
                }
                if ($ret === false) {
                    $this->error($model->getError());
                }
                $this->success('操作成功');
            } else {
                $this->error($model->getError());
            }
            exit;
        }
        
        ${$prikey} = I("get.{$prikey}", 0, 'intval');
        if (${$prikey} > 0) {
            $item = $model->where(array('{$prikey}'=>${$prikey}))->find();
            $this->assign('item', $item);
        }
                  
        $this->display();
    }
    
    /**
     * 删除系统消息
     */
    function delete()
    {
        ${$prikey} = I('get.{$prikey}', 0, 'intval');
    
        $re1 = $this->model->where(array('{$prikey}'=>${$prikey}))->delete();
    
        if ($re1) {
            $this->success("删除成功");
        } else {
            $this->error("删除失败！");
        }
    }

}

