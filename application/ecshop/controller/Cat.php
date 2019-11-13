<?php

namespace app\ecshop\controller;

use think\Controller;
use think\Request;
use app\ecshop\model\Car;//分类表
use app\ecshop\model\Good;//商品表
class Cat extends Base
{
    public function cat_add()
    {
        $cat =Car::select();
        $cat = createTree($cat);
        $this->assign('cat',$cat);
        return $this->fetch();
    }
    //添加
    public function cat_add_do()
    {
    	$data = input('post.');
        if (empty($data['cat_name'])) {
            $this->error('分类名称不能空');
        }
    	$c = new Car;
        // 唯一性
        $name = $data['cat_name'];
        $cc=$c->where('cat_name',$name)->find();
        if ($cc) {
           $this->error('分类名称已存在');
        }
    	$status = $c->save($data);
    	if ($status) {
    		$this->success('添加成功','cat_list');
    	}else{
    		$this->success('添加失败');
    	}
    }
    //查询
    public function cat_list()
    {
        $cat =Car::order('sort_order','asc')->select();
        $cat = createTree($cat);
        // 统计 商品数量
        //语句 select count(*) from goods where cat_id = 13
        foreach ($cat as $key => $value) {
            //取cid
            $cid = $value['cid'];
            // 统计
            $count = Good::where('cid',$cid)->count();
            // var_dump($count);die;
            // 添加 字段
            $cat[$key]['count'] = $count;
        }
        $this->assign('cat',$cat);
        return $this->fetch(); 
    }
    // 删除 
    public function del()
    {
        $id = input('param.cid');
        $c = new Car;
        $cat=$c->where(['parent_id'=>$id])->find();
        // var_dump($cat);die;
        if ($cat) {
           $this->error('分类下面有子类，不能删除,需先删除子类');
        }
        $data = Good::where('cid',$id)->find();
        if ($data) {
           $this->error('分类下面有商品，不能删除');
        }
        $status=$c->destroy($id);
        if ($status) {
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
   
    }
    // 修改
    public function update()
    {
        $cat =Car::select();
        $cat = createTree($cat);
        $this->assign('cat',$cat);
       
        $id = input('param.cid');
        $a = new Car;
        $info=$a->get($id);
        $this->assign('info',$info);
        return $this->fetch();
    }
    // 修改 执行
    public function update_do()
    {
        $id = input('param.cid');
        $data = input('post.');
        $c = new Car;
        $status = $c->save($data,['cid'=>$id]);
        if ($status) {
            $this->success('修改成功','cat_list');
        }else{
            $this->success('修改失败');
        }
    }
}
