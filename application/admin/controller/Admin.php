<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\admin as A;//引入模型admin;类；
use app\admin\Validate\Admin as C;//引入验证器admin;类；
class Admin extends Controller
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
      // var_dump(session('username'));
        return $this->fetch();
   }
   //添加
   public function add_do()
   {
        $date = input('post.');
        //验证
        $k = new C;
        if (!$k->check($date)) {
           $this->error($k->getError());
        }
        //实例化admin
        $a = new A;
        $status = $a->save($date);
        if ($status) {
           $this->success('添加成功','list');
        }else{
            $this->error('添加失败');
        }
   }
   //查询
   public function list()
   {
        $a = new A;
        $list = $a->all()->toarray();
        $this->assign('list',$list);
        return $this->fetch();
   }
   //删除
    public function del()
   {
        $id = input('get.a_id');
        $a = new A;
        $status = $a->destroy($id);
        if ($status) {
           $this->success('删除成功','list');
        }else{
            $this->error('删除失败');
        }
   }
   //修改查询
    public function update()
   {
        $id = input('param.a_id');
        $info = A::get($id);
        $this->assign('info',$info);
        return $this->fetch();
   }
    public function update_do()
   {
        $id = input('post.a_id');
        $date = input('post.');
        $a = new A;
        $status = $a->save($date,['id'=>$id]);
        if ($status) {
           $this->success('修改成功','list');
        }else{
            $this->error('修改失败');
        }
   }















}
