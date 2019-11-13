<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\admin;
class Login extends Controller
{
  
//------------------------------------
    public function add()
    {
        $this->view->engine->layout(false);// 临时关闭当前模板的布局功能
        return $this->fetch();
    }
    //登陆后台
     public function add_do()
    {
       $data =input('post.');//接name值
       //判断 验证码  是否正确
    //-----------------------------------------------------------
       if(!captcha_check($data['code']))
        {
            $this->error('验证码不正确');
        }
    //------------------------------------------------------------------------------
        //判断用户名是否正确
        $a = new admin;
        $info =$a->where('username',$data['username'])->find();//库里的username   ==    接收的username
        if ($info) {
            //判断密码是否正确
            if ($info['userpwd']==md5($data['userpwd'])) {//用库里加密密码 == 接收的加密密码
               session('username',$data['username']);//记录session  看用户是否是登陆状态
               $this->success('登陆成功！',url('admin/add'));
            }else{
                $this->error('密码不正确');
            }
        }else{
            $this->error('用户不正确');
        }
    }
     //-------------------------------------------------------------------------------------
        //退出登陆
        public function tui()
        {
            session(null);
            $this->redirect('Login/add');
        }

}
