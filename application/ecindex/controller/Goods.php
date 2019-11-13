<?php

namespace app\ecindex\controller;

use think\Controller;
use think\Request;
use app\ecindex\model\Good;
use app\ecindex\model\Img;
use app\ecindex\model\History;
use app\ecindex\model\Cary;
class Goods extends Base
{
    /**
     * 购物列表
     *
     * @return \think\Response
     */
    public function index($gid)
    {
        $where =[
            'gid'=>$gid,
            'is_sale'=>1
        ];
        $goods = Good::where($where)->find();
        $this->assign('goods',$goods);

        // 图片
        $img = Img::where('gid',$gid)->select();
        // dump($img);die;
        $this->assign('img',$img);

        // 同步 浏览记录
        $this->addHistory($gid);
        // 同步 购物车
        return view();
    }
    /**
     * 思路：
     * 1： 判断 session 用户是否登陆
     *
     * 未登陆：
     *     cookie
     *
     *      1:先查cookie有无浏览记录 
     *          有： 
     *              检查有无此商品的 浏览记录 
     *              有的话  则更新时间 进行添加
     *              
     *          无：新增
     * 登陆：
     *     db
     *      1:先根据用户id查db有无浏览记录 
     *          有： 
     *              检查有无次商品的 浏览记录 有的话  则更新时间
     *              进行添加
     *          无：新增
     * 
     * @param [type] $gid [description]
     */
    public function addHistory($gid) 
    {
        $res  = $this->checkLogin();
        // dump($res);
        if($res){
            // 添加
           return $this->addDBHistory($gid);
        }else{
            // cookie
           return $this->addCookieHistory($gid);
        }
    }
    /**
     *   游客浏览记录
     * 
    */
    public function addCookieHistory($gid)
    {
        $history = json_decode(cookie('history'),true)?:[];
        // dump($history);
        $array ['g'.$gid]= [
            'gid'=>$gid,
            'create_time'=>time()
        ];
        $data = array_merge($history,$array);
        // dump($data);die;
        cookie('history',json_encode($data));
    }
    // 登录后 的 浏览记录
    public function addDBHistory($gid)
    {
        $where = [
            "r_id"=>session('indexinfo')['r_id'],
            "gid"=>$gid,
        ];
        $res = History::where($where)->find();
        // dump($res);die;
        if ($res) {
            // 更新
            History::where($where)->update(["create_time"=>time()]);
        }else{
            // 新增
            $array = [
                "gid"=>$gid,
                "r_id"=>session('indexinfo')['r_id'],
                "create_time"=>time(), 
            ];
            History::create($array);
        }    
    }

    /**
     *
     * 加入购物车
     * 
    */
    public function addcar()
    {
       $gid = input('post.gid');
       $buy_number = input('post.goods_number');
       // echo $gid;die;
       // echo $buy_number;die;
       $res  = $this->checkLogin();
        // dump($res);
        if($res){
            // 添加
           return $this->addDBcar($gid,$buy_number);
        }else{
            // cookie
           return $this->addCookiecar($gid,$buy_number);
        }
    }
    // cookie 
    public function  addCookiecar($gid,$buy_number)
    {
        $car = json_decode(cookie('car'),true)?:[];
        // dump($car);die;
        $goods = Good::get($gid);
        // dump($goods);die;
        // 判断 库存
        if ($goods['goods_number'] < $buy_number) {
            echo json_encode(['code'=>'1','msg'=>'库存不足']);die;
        }

        // 判断 之间 有没有加入购物车此商品
        if(array_key_exists('car_'.$gid,$car)){
            // dump(['car_'.$gid]['goods_number']);die;
            $car['car_'.$gid]['goods_number'] += $buy_number;
            // dump($car);die;
            if ($goods['goods_number'] < $car['car_'.$gid]['goods_number']) {
                echo json_encode(['code'=>'1','msg'=>'库存不足']);die;
            }
        }else{
            // dump($goods);die;
            $array ['car_'.$gid] = [
                'gid'=>$gid,
                'goods_number'=>$buy_number,
                'shop_price'=>$goods['shop_price'],
                'goods_img'=>$goods['goods_img'],
                'goods_name'=>$goods['goods_name'],
                'create_time'=>time()
            ];
            $car =array_merge($car,$array);
            // dump($data);die;
        }
        $res = cookie('car',json_encode($car));
        // dump($res);die;
        echo json_encode(['code'=>'0','msg'=>'success']);die;
    }  
    /**
     *
     *用户 登陆后 加入购物车
     * 
    */
    public function addDBcar($gid,$buy_number)
    {
        $where = [
            'r_id'=>session('indexinfo')['r_id'],
            'gid'=>$gid,
        ];
        $goods = Good::get($gid);
        if ($goods['goods_number'] < $buy_number) {
            echo json_encode(['code'=>'1','msg'=>'库存不足']);die;
        }
        $car = Cary::where($where)->find();
        // dump($car);die;
        // 判断是否已有此商品的记录 有则更新无则添加
        if ($car) {
           // 更新 购买数量
           $car['goods_number'] +=$buy_number;
           $car['create_time'] = time();
           if ($goods['goods_number'] < $car['goods_number']) {
                echo json_encode(['code'=>'1','msg'=>'库存不足']);die;
            }
           $res =Cary::where('c_id',$car['c_id'])->update($car->toarray());
           // dump($res);die;
        }else{
           // 新增
            $array = [
                'r_id'=>session('indexinfo')['r_id'],
                'gid'=>$gid,
                'goods_number'=>$buy_number,
                'shop_price'=>$goods['shop_price'],
                'goods_img'=>$goods['goods_img'],
                'goods_name'=>$goods['goods_name'],
                'create_time'=>time()
            ];
            $res =Cary::create($array);
        }
       
        if ($res) {
            echo json_encode(['code'=>'0','msg'=>'success']);die;
        }
    }
    /**
     *
     * + - 购物列表
     *
     * 
    */
   public function checkNumber()
   {
        $gid = input('gid');
        $buy_number = input('goods_number');
        // dump ($buy_number);
        // dump ($gid);die;
        // 查询 商品库存
        $goods_number = Good::where('gid',$gid)->value('goods_number');
        if ($goods_number < $buy_number) {
            $buy_number = $goods_number;
        }
        // 判断是否登录
        if ($this->checkLogin()) {
            //登录更改购物车表的更改购物车表的buy_number
            Cary::where(['r_id'=>session("indexinfo")['r_id'],'gid'=>$gid])->update(['goods_number'=>$buy_number]);
        }else{
            // 登录 更改cookie表的buy_number
            $car=json_decode(cookie('car'),true)?:[];
            $car['car_'.$gid]['goods_number'] = $buy_number;
            cookie('car',json_encode($car));
        }
        echo $buy_number;die;
    }
    
}
