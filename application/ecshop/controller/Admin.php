<?php

namespace app\ecshop\controller;

use think\Controller;
use think\Request;
use app\ecshop\model\admin as adminModel;//引入 模型 admin
class admin extends Base
{
    public function add()
    {
        
        return $this->fetch();
    }
    //添加
    public function add_do()
    {
        $data = input('post.');
        if (empty($data['username'])) {
            $this->error('管理员不能为空');
        }
        if (empty($data['userpwd'])) {
            $this->error('密码不能空');
        }
        if (empty($data['usertel'])) {
            $this->error('电话不能空');
        }
        $data['is_nav'] = 0;
        // $data['create_time'] = time();
        $user = new adminModel;
        $status=$user->save($data); // 过滤post数组中的非数据表字段数据
        if ($status) {
            $this->success('添加成功','list');
        }else{
            $this->error('添加失败');
        }
    }
    //查询
     public function list()
    {
        $admininfo = session("info");
        if ($admininfo['user']!=1) {
           $this->error('没有权限进入');die;
        }
        $username =input('get.username');//接username
        $where = [];//定义空的搜索条件
        if (!empty($username)) {//判断 条件
            $where[] =['tp_user.username','like',"%$username%"];
        }
        $a = new adminModel;
        $list = $a->where($where)->paginate(5);
        $page = $list->render();// 获取分页显示
        if(request()->isAjax()){
            $list = $list->toArray();//转化数组
            $list['page']=$page;//页码也返回到前台
            echo json_encode($list);die; //转化json数据
        }
        // 模板变量赋值
        $this->assign('page',$page);
        $this->assign('list',$list);
        $this->assign('username',$username);
        return $this->fetch();
    }
    //即点即改
   public function is_nav()
   {
        $data = input('get.');
        $m = new adminModel;
        $res = $m->update($data);
        // $is_show = input('get.is_nav');
        // $brand_id = input('get.id');
        // // var_dump($brand_id);
        // $k =new User;
        // $res =$k->save(['is_nav'=>$is_show],['id' => $brand_id]);
        
   }

}
