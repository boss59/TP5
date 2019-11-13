<?php

namespace app\ecshop\controller;

use think\Controller;
use think\Request;
use think\facade\Session;//引入  Session 类
use think\facade\Cookie;//引入 Cookie
class Base extends Controller
{
   public function initialize()
   {
        if (!Session::has('info')) {
          	if (Cookie::has('username')) {//三天免登陆
          		  $this->redirect('Login/index');
          	}
           $this->success('请先登陆!',url('Login/index'));
        }
        $this->view->engine->layout(false);
   }
   //---------文件上传----------------------------
    public function upload($filename){
        $file = request()->file($filename); // 获取表单上传文件 例如上传了001.jpg
        $info = $file->move('uploads'); // 移动到框架应用根目录/uploads/ 目录下
        if($info){
           return $info->getSaveName();
        }else{
            return false;
            // echo $file->getError(); // 上传失败获取错误信息
        }
    }
    

}
