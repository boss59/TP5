<?php

namespace app\ecindex\controller;

use think\Controller;
use think\Request;
use app\ecindex\model\Brand;
use app\ecindex\model\Good;
use app\ecindex\model\Car;
use app\ecindex\model\History;
use app\ecindex\model\Cary;
class Lists extends Base
{
    /**
     * 品牌 列表
     *
     * @return \think\Response
    */
    public function index()
    {
    	// 获取 cid 及子分类
    	$cid = input('cid');
    	// var_dump($cid);die;
    	$cate = Car::all()->toArray();
        // dump($cate);die;
        
    	$cate = createTree($cate,$cid);

        // dump($cate);die;
    	$cateids = array_column($cate,'cid');
    	// dump($cateids);die;
    	array_unshift($cateids,$cid);
    	// dump($a);die;
    
    	// 获取 商品品牌 b_id
    	$where[] = [
    		['cid','in',$cateids],
    		['is_sale','=',1]
    	];
    	$goods_brand = Good::where($where)->column('b_id');
    	// dump($goods_brand);die;
    	$goods_brand = array_unique($goods_brand);
    	// dump($goods_brand);die;
    	// 根据 品牌 id获取品牌名称
    	$brand = Brand::field('b_id,brand_name')->where([['b_id','in',$goods_brand]])->select();
    	// dump($brand);die;
    	$this->assign('brand',$brand);

    	// 获取价格 区间
    	$price = Good::where($where)->max('shop_price');
    	$price = $this->getprice($price);
    	$this->assign('price',$price);

    	// 条件 搜索
    	$query = input('get.');
    	// dump($query);die;
    	$s_brand = $query['brand']??'';
    	$s_price = $query['price']??'';
    	$field = $query['field']??1;
    	$orderby = $query['orderby']??2;
    	// dump($field);die;
    	// dump($orderby);die;
    	// 品牌
    	if ($s_brand) {
    		$where[] = ['tp_good.b_id','=',$s_brand];
    	}
    	// 价格
    	if ($s_price) {
    		$price_array = explode('-',$s_price);
    		if (isset($price_array[0])) {
    			$where[] = ['tp_good.shop_price','>=',intval($price_array[0])];
    		}
    		if (isset($price_array[1])) {
    			$where[] = ['tp_good.shop_price','<=',intval($price_array[1])];
    		}
    	}

    	// 默认 销量 价格 新品
    	$order = ['gid'=>'asc'];
    	if ($field) {
    		$field_array = ['1'=>'gid','2'=>'goods_number','3'=>'shop_price','4'=>'is_new'];
    		$order_array = ['1'=>'asc','2'=>'desc'];
    		if ($field==4) {
    			$where [] = ['is_new','=',1];
    		}else{
    			$order = [
    				$field_array[$field]=>$order_array[$orderby]
    			];
    		}
    	}
    	// dump($where);die;
    	// dump($order);die;
        $this->assign('cid',$cid);
    	$this->assign('s_brand',$s_brand);
    	$this->assign('s_price',$s_price);
    	$this->assign('field',$field);

    	// 默认 商品
    	$page =  config('pageSize');
    	$goods = Good::where($where)->order($order)->paginate($page,false,['query'=>$query]);
    	// dump($goods);
    	// echo Good::getLastsql();
    	$this->assign('goods',$goods);

    	// 统计 商品 数量
    	$good = Good::where($where)->count();
    	$this->assign('good',$good);

    	// 获取浏览记录
        $avd = $this->getHistory(); 
        $this->assign('avd',$avd);
    	return view();
  	}
  	/**
  	 *  获取价格 区间
  	*/
 	public function getprice($maxprice)
 	{
 		if (!$maxprice) {
 			return;
 		}
 		$str = [];
 		$start = 1;
 		$avg = ceil($maxprice/7);
 		for ($i=0; $i <7 ; $i++) { 
 			$start = $i*$avg+1;
 			$end = $i==6?$maxprice:$avg*($i+1);
 			$str[] =$start.'-'.$end;
 		}
 		$str[] = $maxprice.'以上';
 		return $str;
 	}
//-----------------------------------浏览记录------------------------------
    /**
     *
     *获取浏览记录 方法
     * 
    */
    public function getHistory()
    {
        $res  = $this->checkLogin();
        // dump($res);
        if($res){
            // 添加
           return $this->getDBHistory();
        }else{
            // cookie
           return $this->getCookieHistory();
        }
    }
    /**
     * 浏览记录 cookie 方法
    */
    public function getCookieHistory()
    {

        $history = json_decode(cookie('history'),true)?:[];
        if(!$history){
            return;
        }
        // dump($history);
        // 多维数组 排序
        array_multisort(array_column($history,'create_time'), SORT_DESC, SORT_NUMERIC, $history);
        $gid = array_column($history,'gid');
        $where = [['gid','in',$gid]];
        // dump($gid);
        $goods =Good::field('gid,goods_name,goods_img,shop_price')->where($where)->order(['gid'=>$gid])->select();
        // dump($goods);
        return $goods;
    }
    // 登录后 的 浏览记录
    public function getDBHistory()
    {
        $where = [
            "r_id"=>session('indexinfo')['r_id']
        ];
        // 查询
        $data =History::where($where)->order('create_time','desc')->limit(5)->select()->toArray();
        if(!count($data)){
            return;
        }
        $gid = array_column($data,'gid');
        $where = [['gid','in',$gid]];
        // dump($gid);
        $goods =Good::field('gid,goods_name,goods_img,shop_price')->where($where)->order(['gid'=>$gid])->select();
         return $goods;
    }
//-------------------------加入购物车----------------------------------
     /**
     *
     * 加入购物车
     * 
    */
    public function addlistcar()
    {
       $gid = input('post.gid');
       $buy_number = input('post.goods_number');
       // echo $gid;die;
       // echo $buy_number;die;
       $res  = $this->checkLogin();
        // dump($res);
        if($res){
            // 添加
           return $this->addlistDBcar($gid,$buy_number);
        }else{
            // cookie
           return $this->addlistCookiecar($gid,$buy_number);
        }
    }
    // cookie 
    public function  addlistCookiecar($gid,$buy_number)
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
        echo json_encode(['code'=>'0','msg'=>'加入成功']);die;
    }  
    /**
     *
     *用户 登陆后 加入购物车
     * 
    */
    public function addlistDBcar($gid,$buy_number)
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
            echo json_encode(['code'=>'0','msg'=>'加入成功']);die;
        }
    }
}
