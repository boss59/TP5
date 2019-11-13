<?php

namespace app\ecindex\controller;

use think\Controller;
use think\Request;
use app\ecindex\model\Coller;
use app\ecindex\model\Good;
class Collert extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $where = [
            "r_id"=>session('indexinfo')['r_id']
        ];
        $coller = Coller::join('tp_good','tp_coller.gid=tp_good.gid')->where($where)->select();
        // dump($coller);die;
        $this->assign('coller',$coller);
        // 件数
        $count = Coller::where($where)->count();
        $this->assign('count',$count);
        return view();
    }

    
}
