<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\category;
class Cate extends Controller
{
   public function __construct()//使用构造函数防止非法登陆
    {
        parent::__construct();
        if(!session('?username'))
        {
            $this->redirect('login/add');
        }
    }
  //-----------------------------------------------
   public function add()
   {
      return $this->fetch();
   }
   //添加
   public function add_do(){
        $date=input('post.');
        $c = new category;//实例化模型类
        $starus=$c->save($date); 
        // var_dump($starus);
        if ($starus) {
            $this->success('添加成功','list');
        }else{
            $this->error('添加失败'); 
        }
   }
   //查询 
    public function list(){
       $c = new category;//实例化模型类
       // $starus=$c->select(); //db类方法  模型都可用
       $list=$c->all()->toarray();//模型方法
       // var_dump($list);
       $this->assign('list',$list);
       return $this->fetch();
   }
   //删除
   public function del(){
       $id = input('get.c_id');
       $c = new category;//实例化模型类
       // $static=$c->where('id',$id)->delete($id);需加条件
       $static=$c->destroy($id);//模型方法
       // var_dump($static);
       if ($static) {
            $this->success('删除成功','list');
        }else{
            $this->error('删除失败'); 
        }
   }
   //修改查询
    public function update(){
       $id = input('param.c_id');
       $c = new category;//实例化模型类
       $info=category::get($id);//模型方法
       // var_dump($info);
       $this->assign('info',$info);
       return $this->fetch();
   }
   //修改执行
    public function update_do(){
        $id =input('post.c_id');
        $date =input('post.');
        $c = new category;//实例化模型类
        // $up=$c->where('c_id',$id)->update($date);//模型方法
        $up=$c->save($date,['id'=>$id]);
        // var_dump($up);
         if ($up) {
            $this->success('修改成功','list');
        }else{
            $this->error('修改失败'); 
        }
   }

















}
