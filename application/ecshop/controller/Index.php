<?php

namespace app\ecshop\controller;

use think\Controller;
use think\Request;
class Index extends Base
{
    //------------------------------------------------------
    //  public function __construct()//使用构造函数防止非法登陆
    // {
    //     parent::__construct();
    //     if(!session('?username'))
    //     {
    //         $this->redirect('Login/index');
    //     }
    //     // var_dump(session('username'));
    // }
    //------------------------------------------------------
    public function index()
    {
        return $this->fetch();
    }
    public function top()
    {
        return $this->fetch();
    }
    public function drag()
    {
        return $this->fetch();
    }
    public function login()
    {
        return $this->fetch();
    }
    public function main()
    {
        return $this->fetch();
    }
    public function menu()
    {
        return $this->fetch();
    }
}
