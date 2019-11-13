<?php

namespace app\ecindex\controller;

use think\Controller;
use think\Request;
use app\ecindex\model\Car;// 分类
use app\ecindex\model\Good;// 商品表
use app\ecindex\model\Coupons;// 优惠券
class Index extends Base
{
	// 首页
	public function index()
	{
       	// 楼逞
       	// 获取 顶级分类
       	$top_name = car::where('parent_id',0)->find()->toArray();
       	$floor = $this->getFloot($top_name['cid']);
       	// dump($floor);die;
              // ========================
              //热门商品
              $best = Good::getGoodsBywhere(['is_best'=>1,'is_sale'=>1]); 
       	// 模板变量赋值
              $this->assign('best',$best);
       	$this->assign('top',$top_name);
       	$this->assign('second',$floor['second']);
       	$this->assign('goods',$floor['goods']);
       	return $this->fetch();
	}
	// ajax 
	public function ajaxgetFloor()
	{
		$cid = input('post.cid');
		$floor_num = input('post.floor_num');
		$floor_num = $floor_num+1;
		// 条件
		$where[] = [
			['parent_id','=',0],
			['cid','>',$cid]
		];
		// 获取 顶级的分类
       	$top_name = car::where($where)->find();
       	if (!$top_name){
       		return;
       	} 
       	// 调用
       	$floor = $this->getFloot($top_name['cid']);
              $this->view->engine->layout(false);
       	// var_dump($floor);die;
       	// 模板变量赋值
       	$this->assign('floor_num',$floor_num);
       	$this->assign('top',$top_name);
       	$this->assign('second',$floor['second']);
       	$this->assign('goods',$floor['goods']);
       	return view('ajaxfloor');
	}
	// -------------方法-------------------
	public function getFloot($cid)
	{
       	// 通过 一级 获取 二级分类
       	$second_cate =car::where('parent_id',$cid)->select()->toArray();
       	//根据 大分类获取 商品
       	// 先 获取 当前 大分类 下的 子分类
       	$cate_data = Car::all()->toArray();
       	$cate_data = createTree($cate_data,$cid);
              // dump($cate_data);die;
       	$cateids = array_column($cate_data,'cid');
       	// dump($cateids);die;
       	array_unshift($cateids,$cid);
       	// dump($a);die;
       	//获取 商品
       	$where = [
       		['cid','in',$cateids]
       	];
       	$goods = Good::getGoodsBywhere($where);
       	// 组合数组
       	return ['second'=>$second_cate,'goods'=>$goods];
	}
}
