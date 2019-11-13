<?php

namespace app\ecshop\controller;

use think\Controller;
use think\Request;
use app\ecshop\model\Regist;
class Customer extends Controller
{
    public function list()
    {
        $m = new Regist;
        $list = $m->select();
        $this->assign('list',$list);
        $this->view->engine->layout(false);
        return view();
    }
    public function status()
    {
        $data = input('param.');
        $s = new Regist;
        $res = $s->update($data);
    }

}
