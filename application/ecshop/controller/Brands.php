<?php

namespace app\ecshop\controller;

use think\Controller;
use think\Request;
use app\ecshop\model\Brand;//引入 品牌 模型Brand
class Brands extends Base
{
    public function brand_add()
    {
        return $this->fetch();
    }
    //添加s
    public function brand_add_do()
    {
        $postData = input('post.');//接 name 值
        //-------------文件上传------------------
        $postData['brand_logo']="";
        if (!empty($_FILES['brand_logo']['name'])) {
            $img_path ='/uploads/'.$this->upload('brand_logo');
            $postData['brand_logo'] = $img_path;
        }
        $b = new Brand;
        // 唯一性
        $name = $postData['brand_name'];
        $cc = $b->where('brand_name',$name)->find();
        if ($cc) {
            $res = ['ret'=>0,'msg'=>'品牌已存在'];
            echo json_encode($res);die;
        }
        $status=$b->allowField(true)->save($postData);// 过滤post数组中的非数据表字段数据
        // =========ajax 添加===========
        if ($status) {
            $return = ['ret'=>1,'msg'=>'添加成功'];
        }else{
            $return = ['ret'=>0,'msg'=>'添加失败'];
        }
        echo json_encode($return);die;
        
    }
    //查询
    public function brand_list()
    {
        $username =input('get.brand_name');//接username
        $where = [];//定义空的搜索条件
        if (!empty($username)) {//判断 条件
            $where[] =['tp_brand.brand_name','like',"%$username%"];
        }
        $b = new Brand;
        $list=$b->where($where)->paginate(8);
        $page = $list->render();// 获取分页显示
         if (request()->isAjax()) {
            $list = $list->toArray();//转化数组
            $list['page']=$page;//页码也返回到前台
            echo json_encode($list);die; //转化json数据
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('username',$username);
        return $this->fetch();
    }
    //删除
     public function del()
   {
        $id = input('param.b_id');
        $c = new Brand;
        $status=$c->destroy($id);
        if ($status) {
            echo 1;die;
       }else{
            echo 0;die;
       }
   }
   //修改 查询
   public function update()
   {
        $id = input('param.b_id');
        $a = new Brand;
        $info=$a->get($id);
        $this->assign('info',$info);
        return $this->fetch();
   }
   //修改 执行
   public function update_do()
   {
        $id = input('param.b_id');
        $postData = input('post.');//接 name 值
    //-------------文件上传----------------------------------------------
        // var_dump($_FILES['brand_name']);die;
        $img_path="";
        if (!empty($_FILES['brand_logo']['name'])) {
            $img_path ='/uploads/'.$this->upload('brand_logo');
            // var_dump($img_path);die;
        }
       $postData['brand_logo'] = $img_path;
    //--------------------------------------------------------------------
        $b = new Brand;
        // 过滤post数组中的非数据表字段数据
        $status=$b->allowField(true)->save($postData,['b_id'=>$id]);
        if ($status) {
            $this->success('修改成功','brand_list');
        }else{
            $this->error('修改失败');
        }
   }
   //即点即改
   public function is_show()
   {
        // $is_show = input('get.is_show');
        // $brand_id = input('get.b_id');
        // $k =new Brand;
        // $k->save(['is_show' => $is_show],['b_id' => $brand_id]);
        $data = input('param.');
        $m = new Brand;
        $res = $m->update($data);
   }


}
