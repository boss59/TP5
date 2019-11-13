<?php

namespace app\ecshop\controller;

use think\Controller;
use think\Request;
use app\ecshop\model\Coupons;
use app\ecshop\model\Car;
class Cou extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $this->view->engine->layout(false);
        // 分类表
        $cat=car::select();
        $cat = createTree($cat);
        $this->assign('cat',$cat);
        return view();
    }
    // 添加
    public function add_do()
    {
        $data = input('post.');
        $m = new Coupons;
        $status = $m->save($data);
        if ($status) {
           $this->success('添加成功','list');
        }else{
            $this->success('添加失败');
        }
    }
    // 查询
    public function list()
    {
        $m = new Coupons;
        $cou = $m->select();
        $this->assign('cou',$cou);
        $this->view->engine->layout(false);
        return view();
    }
}
