<?php

namespace app\ecindex\controller;

use think\Controller;
use think\Request;
use app\ecindex\model\Region;
use app\ecindex\model\User_address;
class Address extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $r_id = session('indexinfo')['r_id'];
        $user_address = User_address::where('r_id',$r_id)->select();
        
        // 循环 地址
        foreach ($user_address as $k => $v) {
            $user_address[$k]['country'] = Region::getAddressName($v['country']);
            $user_address[$k]['province'] = Region::getAddressName($v['province']);
            $user_address[$k]['city'] = Region::getAddressName($v['city']);
            $user_address[$k]['district'] = Region::getAddressName($v['district']);
        }
        // dump($user_address);die;
        $region = Region::where('parent_id',0)->select();
        $this->assign('region',$region);
        $this->assign('user_address',$user_address);
        return view();
    }
    // 四级联动
    public function getsonAddress()
    {
        $parent_id = input('post.parent_id');
        $data = Region::where('parent_id',$parent_id)->select();
        echo json_encode($data);die;
    }
   
}
