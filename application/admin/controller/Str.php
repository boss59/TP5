<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\news;
class Str extends Controller
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
        $date = input('post.');
        $file = request()->file('photo');// 获取表单上传文件
        $info = $file->move('uploads');
        $date['photo']='\\uploads\\'.$info->getSaveName();
        // var_dump($date);die;
        $n = new news;
        $status = $n->save($date);
        // var_dump($status);die;
        if ($status) {
           $this->success('添加成功','list');
        }else{
           $this->error('添加失败');
        }
   }
   //查询
    public function list()
   {
       $n = new news;
       $list = $n->all()->toArray();
       $this->assign('list',$list);
       return $this->fetch();
   }
   //删除
   public function del()
   {
        $id=input('get.n_id');
        $n = new news;
        $del = $n->destroy($id);
       if ($del) {
           $this->success('删除成功','list');
        }else{
           $this->error('删除失败');
        }
   }
   //修改查询
   public function update()
   {
        $id=input('param.n_id');
        $n = new news;
        $info = news::get($id);
        $this->assign('info',$info);
        return $this->fetch();
   }
    //修改执行
   public function update_do()
   {
        $id=input('param.n_id');
        $date=input('param.');
        // var_dump($_FILES['photo']);die;
        if($_FILES['photo']['error']!=4){
            $file = request()->file('photo');// 获取表单上传文件
            $info = $file->move('uploads');
            $date['photo']='\\uploads\\'.$info->getSaveName();
        }
        $n = new news;
        $status = $n->save($date,['id'=>$id]);
        // var_dump($status);die;
         if ($status) {
           $this->success('修改成功','list');
        }else{
           $this->error('修改失败');
        }
   }












}
