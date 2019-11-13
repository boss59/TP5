<?php

namespace app\ecindex\controller;

use think\Controller;
use think\Request;
use app\ecindex\model\Car;// 分类
use app\ecindex\model\Coupons;// 优惠券
use app\ecindex\model\Cary;// 分类
class Base extends Controller
{
    public function __construct()//使用构造函数防止非法登陆
    {
        parent::__construct();
        // 导航
        $nav = car::where('parent_id',0)->select();
        $this->assign('nav',$nav);
        // 商品分类
        $catData = Car::select()->toArray();
        $catData = createTreeBySon($catData);
        $this->assign('catData',$catData);
        // 优惠券
        $Coupons = Coupons::select()->toArray();
        $this->assign('Coupons',$Coupons);
        // 获取当前控制器
        $controller = \Request::controller();
        $action = \Request::controller();
        $this->assign('controller',$controller);
        $this->assign('action',$action);
        // 购物单
        $car = $this->getcar();
        $this->assign('car',$car);
        //统计 购物单 数量
        $cot = $this->getcount();
        $this->assign('cot',$cot);
        // 统计 总价钱 数量
        if (!$this->checkLogin()) {
            $car = json_decode(cookie('car'),true)?:[];
            if (!count($car)) {
                $monay = number_format(0,2,'.','');
                $this->assign('monay',$monay);
            }else{
                $monay = 0;
                foreach ($car as $k => $v) {
                    $monay +=  $v['goods_number']*$v['shop_price'];
                }
                $monay = number_format($monay,2,'.','');
                $this->assign('monay',$monay);
            }
        }else{
            $r_id=session('indexinfo')['r_id'];
            $monay = Cary::getMoneys($r_id);
            $monay = number_format($monay,2,'.','');
            $this->assign('monay',$monay);
        }
    }
    // 判断 是否是登陆状态
    public function checkLogin()
    {
        $user = session('indexinfo');
        // dump($user);die;
        if (!$user) {
            return false;
        }
            return true;
    }
     /**
     * 购物列表
     * @return [type] [description]
     */
    public function getcar()
    {
        $res  = $this->checkLogin();
        // dump($res);
        if($res){
            // 添加
           return $this->getDBcar();
        }else{
            // cookie
           return $this->getCookiecar();
        }
    }
    //  游客状态
    public function getCookiecar()
    {
        $car = json_decode(cookie('car'),true)?:[];
        // dump($car);
        array_multisort(array_column($car,'create_time'), SORT_DESC, SORT_NUMERIC, $car);
        return $car;
    }
    // 登陆 后的 购物列表
    public function getDBcar()
    {
        $where = [
            "r_id"=>session('indexinfo')['r_id'],
        ];
        $data = Cary::where($where)->order('create_time','desc')->select();
        return $data;
    }
    /**
     *
     *  统计 购物单 数量
     *
     * 
     */
    public function getcount()
    {
        $res  = $this->checkLogin();
        // dump($res);
        if(!$res){
            // 添加
           return $this->getDBcount();
        }else{
            // cookie
           return $this->getCookiecount();
        }
    }
    public function getDBcount()
    {
        $car = json_decode(cookie('car'),true)?:[];
        if (!count($car)) {
            return 0;
        }
        return count($car);
    }
    public function getCookiecount()
    {
        $where = [
            "r_id"=>session('indexinfo')['r_id']
        ];
        $count =  Cary::where($where)->count();
        return $count;
    }
}
