<?php

namespace app\ecindex\controller;

use think\Controller;
use think\Request;
use app\ecindex\model\Cary;
use app\ecindex\model\Coller;
class Car extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
    	// 列表
        return view();
    }
   
    /**
     *
     *ajax  请求方法
     * 
    */
    public function getMoney()
    {
        $gid = input('post.gid');
        if (!$gid) {
            return number_format(0,2,'.','');
        }
        // dump ($gid);
        // 判断 是否登录
        if ($this->checkLogin()) {
            if (is_array($gid)) {
                $gid = implode(',',$gid);
            }
            // 登录 Db计算
            $price = Cary::getMoney($gid);
        }else{
            // 未登录 cookie计算
            $price = $this->getCookieMoney($gid);
        }
        echo $price;
    }
    // 未登录 cookie计算 的 方法
    public function getCookieMoney($gid)
    {
        // dump($gid);
        $car = json_decode(cookie('car'),true)?:[];
        // dump($car);
        if (!$car) {
            return number_format($total,2,'.','');
        }
        $total = 0;
        foreach ($gid as $k => $v) {
            $total +=  $car['car_'.$v]['goods_number']*$car['car_'.$v]['shop_price'];
        }
        return number_format($total,2,'.','');
    }
    /**
     * 
     * 
     * 
     * 单删 批删 的 ajax
     * @return [type] [description]
     */
    public function delete()
    {
        $gid = input('post.gid');
        // dump($gid);die;
        if (!$gid) {
            return;
        }
        if ($this->checkLogin()) {
            // dB 删除
            $where[] = [
                ['r_id','=',session('indexinfo')['r_id']],
                ['gid','in',$gid]
            ];
            $res=Cary::where($where)->delete();
        }else{
            // cookie 删除
            $car = json_decode(cookie('car'),true)?:[];
            if (strpos($gid,',')) {
                $gid_arr = explode(',',$gid);
                // print_r($gid_arr);
                foreach ($gid_arr as  $v) {
                    unset($car['car_'.$v]);
                }
            }else{
                // 单删
                unset($car['car_'.$gid]);
            }
            // dump($car);die;
            cookie('car',json_encode($car));
        }
        echo json_encode(['code'=>'0','msg'=>'删除成功']);die;
    }
    /**
     *
     * 收藏夹
     *
     * 
     */
    public function addcoller()
    {
        $gid = input('post.gid');
        // echo $gid;die;
        if (!$this->checkLogin()) {
            // 未登录 提醒
            echo json_encode(['code'=>'1','msg'=>'请先登录，是否进行？']);die;
        }else{
            // 查询
            $where = [
                ['r_id','=',session('indexinfo')['r_id']],
                ['gid','in',$gid]
            ];
            $count = Coller::where($where)->count();
            // dump($count);die;
            if ($count) {
                echo json_encode(['code'=>'3','msg'=>'已经收藏过了']);die;
            }
                // 收藏
                $array = [
                    "r_id"=>session('indexinfo')['r_id'],
                    'gid'=>$gid,
                    'create_time'=>time()
                ];      
                $res = Coller::create($array); 
                if ($res) {
                    echo json_encode(['code'=>'0','msg'=>'收藏成功！']);die;
                }   
        }
    }
}
