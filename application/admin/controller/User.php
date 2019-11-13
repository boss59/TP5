<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\dao;
class User extends Controller
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
   public function add_do()
   {
       $data=input('post.');
       $x = new dao;//实例化模型类 
       $static=$x->save($data);
       // var_dump($static);die;
       if ($static) {
          $this->success('添加成功','list');
       }else{
           $this->error('添加失败');
       }
   }
   //查询
    public function list()
   {
       $x = new dao;//实例化模型类 
       $list=$x->all();
       $this->assign('list',$list);
       return $this->fetch();
   }
   //删除
    public function del()
   {
       $id=input('get.q_id');
       $x = new dao;//实例化模型类 
       $del=$x->destroy($id);
       if ($del) {
          $this->success('删除成功','list');
       }else{
           $this->error('删除失败');
       }
       
   }
   //修改查询
    public function update()
   {
       $id=input('param.q_id');
       $x = new dao;//实例化模型类 
       $info=dao::get($id);
       $this->assign('info',$info);
       return $this->fetch();
   }
    //修改执行
    public function update_do()
   {
       $date =input('post.');
       $id=input('post.q_id');
       $x = new dao;//实例化模型类 
       $static=$x->save($date,['id'=>$id]);
       // var_dump($static);
      if ($static) {
          $this->success('修改成功','list');
       }else{
           $this->error('修改失败');
       }
   }





















}
