<?php

namespace app\ecshop\controller;

use think\Controller;
use think\Request;

class Comments extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $this->view->engine->layout(false);
        return view();
    }
    // 添加
    // public function add_do()
    // {

    // }
   
}
