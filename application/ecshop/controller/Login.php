<?php

namespace app\ecshop\controller;

use think\Controller;
use think\Request;
use think\captcha\Captcha;//引入 验证码
use app\ecshop\model\admin;//引入 模型 admin
use app\ecshop\validate\V;//引入 验证器 v
use think\facade\Session;//引入  Session
use think\facade\Cookie;//引入 Cookie
class Login extends controller
{
   public function index()
    {
        //读取 cookie 
        $info=Cookie::get('username');
        // dump($info);
        //判断 cookie值 有值 做过登陆操作
        if ($info) {
            $info = json_decode($info,true);//转化 数组
            session::set('info',$info);//记录session  看用户是否是登陆状态
            $this->redirect("index/index");die; 
        }
        $this->view->engine->layout(false);
        return $this->fetch('/index/login');
    }
    //验证码
    public function verify()
    {
        //4位数字
        $config = [
            // 验证码字体大小
            'fontSize' => 40,
            // 验证码位数
            'length' => 4,
            // 关闭验证码杂点
            'useNoise' => false,
            //数字
            'codeSet' => '0123456789',
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }
//=======================================分开判断=======================================
    //登陆
    public function add_do()
    {
        $remember = input('post.remember');//接三天免登陆的值
        // var_dump($remember);die;
        $data =input('param.');//接name值
    //------------判断 验证码  是否正确------------------------
       // if(!captcha_check($data['captcha']))
       //  {
       //      $this->error('验证码不正确');
       //  }
    //-----------------------验证非空----------------------------
        $k = new V;
        if (!$k->check($data)) {
           $this->error($k->getError());
        }
    //----------------判断用户名是否正确---------------------
        $a = new admin; // $info=$a->save($data); // var_dump($info);
        $info =$a->where('username',$data['username'])->find();//库里的username   ==    接收的username
        // var_dump(session('username.user'));die;
        if ($info['is_nav']!=1) {
            $this->error('该账户已停用');die;
        }
        if ($info) {
            //判断密码是否正确
            if ($info['userpwd']==md5($data['userpwd'])) {//用库里加密密码 == 接收的加密密码
                if ($remember == 1) {// 判断 用户 勾选三天免登陆
                    Cookie::set('username',$info,3600);//设置 cookie
                }
               session::set('info',$info);//记录session  看用户是否是登陆状态
               $this->success('登陆成功！',url('index/index'));
            }else{
                $this->error('密码不正确');
            }
        }else{
            $this->error('用户不正确');
        }

    }
//=========================组合判断================================================
        // public function add_do()
        // {
        //     //接 name 值
        //     $username = input('post.username');
        //     $userpwd = input('post.userpwd');
        //     $captcha = input('post.captcha');
        //     // 验证码 
        //     if(!captcha_check($captcha)){
        //        $this->error('验证码不正确');
        //     }
        //     // 查数据库 
        //     $a = new admin;
        //     $info = $a->where(["username"=>$username,"userpwd"=>md5($userpwd)])->find();
        //     //判断
        //     if ($info) {
        //         Session::set('username',$info);//记录session  看用户是否是登陆状态
        //         $this->success('登陆成功！',url('index/index'));
        //     }else{
        //        $this->error('用户或密码不正确'); 
        //     }
        // }
    //---------------------------------退出登陆------------------------
    public function tui()
    {
        session(null);//销毁 session
        Cookie::delete('username');// 销毁 Cookie
        $this->success('臣退了!',url('Login/index'));
    }











}
