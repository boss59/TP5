<?php

namespace app\kaoshi\controller;

use think\Controller;
use think\Request;
use app\kaoshi\model\Goods as GoodsModel;
use app\kaoshi\model\Cary as CaryNodel;
class Cary extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $where = [
            "is_up"=>1,
        ];
        $goodsinfo = GoodsModel::where($where)->paginate(8);
        $this->assign('goodsinfo',$goodsinfo);
        $this->view->engine->layout(false);
        return view();
    }
    public function getCary()
    {
        $data = input('post.');
        if ($this->checkLogin()) {
           return $this->getCarydb($data);
        }else{
           return $this->getCaryCookie($data);
        }
    }
    public function getCarydb($data)
    {
        // dump($data);die;
        $data['user_id'] = 1;// 获取 用户 id
        $where = [
            ['user_id','=',$data['user_id']],
            ['goods_id','=',$data['goods_id']],
            ['is_del','=',1]
        ];
        // 判断当前用户是否已经买过此商品
        $CaryModel = model('Cary');
        $info=$CaryModel->where($where)->find();

        if (!empty($info)) {
            // 验证 库存
            $result=$this->checkBuynumber($data['goods_id'],$data['buy_number'],$info['buy_number']);
            if (is_array($result)) {
                return $result;
            }

            // 更新时间 更新购买数量
            $info['buy_number'] += $data['buy_number'];
            $info['add_time'] = time();
            $res=$CaryModel->where($where)->update($info->toArray());
            if ($res) {
                return ['font'=>'加入购物车成功','code'=>1];
            }else{
                return ['font'=>'加入购物车失败','code'=>2];
            }
        }else{
            // 验证 库存
            $result=$this->checkBuynumber($data['goods_id'],$data['buy_number']);
            if (is_array($result)) {
                return $result;
            }
            // 新增
            $data['add_time'] = time();
            $res=$CaryModel->insert($data);
            if ($res) {
                return ['font'=>'加入购物车成功','code'=>1];
            }else{
                return ['font'=>'加入购物车失败','code'=>2];
            }
        }
    }
    /**
     * 未登录 购物车 存cookie
     * @param [type] $data [description]
     */
    public function getCaryCookie($data)
    {
        $cartinfo = cookie('cartinfo')?:[];
        // dump($cartinfo);die;

        $goods_id = $data['goods_id'];// 加入 购物车的id
        $buy_number = $data['buy_number'];//商品将要 加入 的数量
        if (!empty($cartinfo)) {
            // 检测 之前 是否 加入过 购物车
            if (array_key_exists($goods_id,$cartinfo)) {
                $aleray_number = $cartinfo[$data['goods_id']]['buy_number'];//商品已经 加入 的数量
                // 检测 -- 累加
                $result=$this->checkBuynumber($goods_id,$buy_number,$aleray_number);
                if (is_array($result)) {
                    return $result;
                }

                // 加 购买数量
                $cartinfo[$goods_id]['buy_number']=$aleray_number + $buy_number;
                $cartinfo[$goods_id]['add_time'] = time();
            }else{
                // 检测 -- 增加
                $result=$this->checkBuynumber($goods_id,$buy_number);
                if (is_array($result)) {
                    return $result;
                }
                $cartinfo[$data['goods_id']] = $data;
                $cartinfo[$goods_id]['add_time'] = time();
            }
        }else{
            // 检测 -- 增加
            $result=$this->checkBuynumber($goods_id,$buy_number);
            if (is_array($result)) {
                return $result;
            }
            $cartinfo[$data['goods_id']] = $data;
            $cartinfo[$goods_id]['add_time'] = time();
        }
        // dump($cartinfo);die;
        cookie('cartinfo',$cartinfo);
        return ['font'=>'加入购物车成功','code'=>1];
    }
    // 判断 库存
    public function checkBuynumber($goods_id,$buy_number,$aleray_number=0)
    {
        // 根据 商品 id 查询 商品表 得到 库存
        $goodsModel = model('Goods');
        $goods_number=$goodsModel->where('goods_id',$goods_id)->value('goods_num');
        if (($buy_number + $aleray_number) > $goods_number) {
           return ['font'=>'库存不足,最多只能买'.($goods_number-$aleray_number).'件','code'=>2,'goods_number'=>$goods_number];
        }else{
            return true;
        }
    }
    public function checkLogin()
    {
        // return session('?userinfo');
        // session('user_id',1);

        if(session('user_id')){
            return true;
        }else{
            return false;
        }
    }
//------------------------ 列表 ---------------------------------------
    public function list()
    {
        $this->view->engine->layout(false);
        $list = $this->listcary();
        // 总价
        $total = $this->gettotal();
        // dump($total);die;
        $this->assign('total',$total);
        $this->assign('list',$list);
        return view();
    }
   
    public function listcary()
    {
        if ($this->checkLogin()) {
           return $this->listCarydb();
        }else{
           return $this->listCaryCookie();
        }
    }
    public function listCarydb()
    {
        $caryModel = model('Cary');
            $where = [
                ["user_id",'=',1],
                ['is_del','=',1],
            ];
        $list = $caryModel
        ->field('shop_cary.goods_id,buy_number,add_price,goods_name,goods_price,goods_num,goods_img,is_up')
        ->where($where)
        ->join('shop_goods','shop_cary.goods_id=shop_goods.goods_id')
        ->order('shop_cary.add_time',"desc")
        ->select();
        // dump($list);die;
        if (!empty($list)) {
            return $list;
        }else{
            return false;
        }     
    }
    public function listCaryCookie()
    {
         // 取值
        $cartinfo = cookie('cartinfo')?:[];
        // dump($cartinfo);die;
        if (!empty($cartinfo)) {
            $goods_id = [];
            // 多维数组 排序
            foreach ($cartinfo as $k => $v) {
                $order[$k]=$v['add_time'];
            }
            array_multisort($order,SORT_DESC,$cartinfo);
            
            // 获取 商品id
            $goods_id=array_column($cartinfo,'goods_id');
            $goods_id = implode(',',$goods_id);
            $where = [
                ['goods_id','in',$goods_id],
            ];
            // 自定义 排序
            $goodsModel = model('Goods');
            $exp =  new \think\db\Expression("field(goods_id,$goods_id)"); 
            $goodsinfo=$goodsModel->where($where)->order($exp)->select()->toArray();
            // dump($goodsinfo);die;
            foreach ($goodsinfo as $k => $v) {
                $goodsinfo[$k] = array_merge($v,$cartinfo[$k]);
            }
           return $goodsinfo;
        }else{
            return false;
        }
    }
    // 总价
    public function gettotal()
    {
        if ($this->checkLogin()) {
           return $this->gettotalDB();
        }else{
           return $this->gettotalcookie();
        }
        echo $totalprices;
    }
    // 数据库 总价
    public function gettotalDB()
    {
       $caryinfo = CaryNodel::where('user_id',1)->select()->toArray();
       $goods_id = array_column($caryinfo,'goods_id');
       $goods_id = implode($goods_id,',');
        $where = [
            ['user_id','=',1],
            ['c.goods_id','in',$goods_id],
            ['is_del','=',1],
        ];
        $info = model('Cary')
        ->field('buy_number,goods_price')
        ->alias('c')
        ->join('goods g','c.goods_id=g.goods_id')
        ->where($where)
        ->select()->toArray();
        $count=0;
        foreach ($info as $k => $v) {
            $count+=$v['goods_price']*$v['buy_number'];
        }
        return $count;
    } 
    public function gettotalcookie()
    {
        $cartinfo = cookie('cartinfo')?:[];
        $goods_id = array_column($cartinfo,'goods_id');
        $carinfotwo = cookie('cartinfo')?:[];
        $info = [];
        if (!empty($carinfotwo)) {
            // 处理数组 得到 id 数量
            foreach ($carinfotwo as $k => $v) {
                if (in_array($v['goods_id'],$goods_id)) {
                   $info[$k]['goods_id'] = $v['goods_id'];
                   $info[$k]['buy_number'] = $v['buy_number'];
                }
            }
            // dump($info);die;
            // 根据 商品 id 得到 商品价格
            $goodsModel = model('Goods');
            $where = [
                ['goods_id','in',$goods_id],
                ['is_up','=',1]
            ];
            $goodsinfo = $goodsModel->field('goods_id,goods_price')->where($where)->select()->toArray();
            // dump($goodsinfo);die;
            $total = 0;
            foreach ($info as $k => $v) {
                foreach ($goodsinfo as $key => $val) {
                    if ($k == $val['goods_id']) {
                       // $info[$k]['goods_price'] = $val['goods_price']*$v['buy_number'];
                       $total += $val['goods_price']*$v['buy_number'];
                    }
                }
            }
            // echo $total;die;
            return number_format($total,2,'.','');
        }else{
            return number_format(0,2,'.','');
        }
        

    }
}
