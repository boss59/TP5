<?php

namespace app\ecshop\controller;

use think\Controller;
use think\Request;
use app\ecshop\model\Good;// 商品表 模型
use app\ecshop\model\Brand;// 品牌表 模型
use app\ecshop\model\Car;// 分类表 模型
use app\ecshop\validate\Goo;// 引入 验证 Goo
use app\ecshop\model\Img;// 引入 相册表 模型
class Goods extends Base
{
    public function goods_add()
    {
        // 品牌 表
        $b = new Brand;
        $brand = $b->field('b_id,brand_name')->all();
        $this->assign('brand',$brand);
        // 分类 表
        $cat=car::select();
        $cat = createTree($cat);
        $this->assign('cat',$cat);
        //返回    
        return $this->fetch();
    }
    // 添加
    public function goods_add_do()
    {
        $data = input('post.');
        // 货号
        $data['goods_sn'] = $this->sn();
        // 文件 上传
        $data['goods_img']="";
        if (!empty($_FILES['goods_img']['name'])) {
            $img_path='/uploads/'.$this->upload('goods_img');
            $data['goods_img']=$img_path;
        }
        //验证
        $v = new Goo;
        if (!$v->check($data)) {
            $this->error($v->getError());
        }
        if(empty($data['cid'])){
            $this->error('分类必选');
        }
        if(empty($data['b_id'])){
            $this->error('品牌必选');
        }
        // 实例化 商品表
        $g = new Good;
        $status=$g->allowField(true)->save($data);
        // ---相册----
        $gid = $g->gid;//获取商品表的id
        // var_dump($_FILES['img_url']['name']);die;
        if (!empty($_FILES['img_url']['name'][0])) {
        // var_dump($gid);die;
            $imgArr = $this->uploadAll();
            // dump($imgArr);die;
            $list = [];//定义循环添加的数据
            foreach($data['img_desc'] as $k => $v){
                $list[] = [
                    'img_desc' =>$v,
                    'gid' =>$gid,
                    'img_url' =>$imgArr[$k]
                ];
            }
            $Img = new Img;
            $Img->saveAll($list);
        }
     
        // 添加成功
        if ($status) {
            $this->success('添加成功','goods_list');
        }else{
            $this->error('添加失败');
        }
    }
    // 多文件上传
    public function uploadAll(){
    // 获取表单上传文件
        $files = request()->file('img_url');
        $imgArr = [];//定义数组
        foreach($files as $file){
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->move('uploads');
            if($info){
                $imgArr[]='/uploads/'.$info->getSaveName();
            }else{
                return false;
            }
        }
        return $imgArr;
    }
    // 查询
     public function goods_list()
    {
        $where = [];//定义 搜索条件
        // 分类表  条件搜索
        $cid = input('get.cid');
        if (input('get.cid')) { 
           $where[]=['tp_good.cid','=',input('get.cid')];
        }
        $b_id = input('get.b_id');
        // 品牌 表 条件搜索
        if (input('get.b_id')) {
           $where[]=['tp_good.b_id','=',input('get.b_id')];
        }
        // 关键字 条件搜索
        $username = input('get.goods_name');
        if (input('get.goods_name')){
           $where[]=['tp_good.goods_name','like',"%$username%"];
        }
        // 上架  条件搜索
        $is_sale = input('get.is_sale');
        if (input('get.is_sale')) {
            $where[]=['tp_good.is_sale','=',input('get.is_sale')];
        }
        //------------------------查询---------------------------------
         // 品牌 表 
        $b = new Brand;
        $brand = $b->field('b_id,brand_name')->all();
        $this->assign('brand',$brand);
        // 分类 表
        $cat=car::select();
        $cat = createTree($cat);
        $this->assign('cat',$cat);
        // 商品表
        $b = new Good;
        $list = $b->where($where)->join('tp_car c','tp_good.cid=c.cid')->join('tp_brand v','tp_good.b_id=v.b_id')->paginate(3);
        $page = $list->render();// 获取分页显示
         if (request()->isAjax()) {
            $list = $list->toArray();//转化数组
            $list['page']=$page;//页码也返回到前台
            echo json_encode($list);die; //转化json数据
        }
        // 模板变量赋值
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('username',$username);
        $this->assign('cid',$cid);
        $this->assign('b_id',$b_id);
        $this->assign('is_sale',$is_sale);
        return $this->fetch();
    }
      //删除
    public function del()
    {
        $id = input('param.gid');
        $c = new Good;
        $status=$c->destroy($id);
        if ($status) {
            echo 1;die;
       }else{
            echo 0;die;
       }
    }
    //即点即改
   public function is()
   {
        // $is_show = input('get.is_show');
        // $brand_id = input('get.b_id');
        // $k =new Brand;
        // $k->save(['is_show' => $is_show],['b_id' => $brand_id]);
        $data = input('param.');
        $m = new Good;
        $res = $m->update($data);
   }
    // 唯一 货号
    public function sn()
    {
        return "DNF".date("YmdHis").rand(1000,9999);
    }
}
