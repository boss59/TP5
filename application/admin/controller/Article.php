<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\articles;//引入模型articles类
use app\admin\model\category;//引入模型category类
use app\admin\model\dao;//引入模型dao类
use app\admin\validate\Articlex;//引入验证器Articlex类
class Article extends Controller
{
   public function __construct()//使用构造函数防止非法登陆
    {
        parent::__construct();
        if(!session('?username'))
        {
            $this->redirect('login/add');
        }
    }
  //-----------------------------------------------
   public function add()
   {
        //实例化分类category类
        $c = new category;
        $cate = $c->field('c_id,title')->all(); 
        $this->assign('cate',$cate);
        //实例化标签dao类
        $c = new dao;
        $dao = $c->field('q_id,shen')->all(); 
        $this->assign('dao',$dao);
        return $this->fetch();
   }
   //添加
   public function add_do()
   {
       $data =input('post.');//接name值
       // var_dump($data);die;
    //-------=-----------------------------------------------------------
       //文件上传
       if (input('?file.image')) {
            $file = request()->file('image');//接收上传的name值
            $info = $file->move( './uploads');// 移动到框架应用根目录/uploads/ 目录下
            if($info){
                $data['image']= '/uploads/'.$info->getSaveName();
            }else{
                echo $file->getError();// 上传失败获取错误信息
            }
       }else{
            $this->error('没有上传文件');
       }
    //-------------------------------------------------------------------------
    // 验证非空。。。。。。。
        $k = new Articlex;
        if (!$k->check($data)) {
           $this->error($k->getError());
        }
    //--------------------------------------------------------------------------
       //实例化内容articles类// $data['tag_ids'] = implode(',',$data['tag_ids']);
       $a = new articles;
       $status = $a->save($data);// var_dump($status);die;
       if ($status) {
          $this->success('添加成功','list');
       }else{
          $this->error('添加失败');
       }
   }
   //查询
   public function list()
   {    
        //实例化分类category类
        $c = new category;
        $cate = $c->field('c_id,title')->all(); 
        $this->assign('cate',$cate);
//----------------------------------------------------------------------
        //分页搜索
        $where = [];//定义搜索条件的数组
        $query = [];//定义分页参数的数组
        if (input('?get.cid')) {//判断有没有搜索cid的，有的话弄个cid的搜索条件，弄个cid的分页参数
           $where[]=['cid','=',input('get.cid')];
           $query['cid']=input('get.cid');
        }
        if (input('?get.a_title')){//判断有没有搜索a_title的，有的话弄个a_title的搜索条件，弄个a_title的分页参数
           $where[]=['a_title','like','%'.input('get.a_title').'%'];
           $query['a_title']=input('get.a_title');
        }
//----------------------------------------------------------------------
        //实例化内容articles类
        $a = new articles;
        $list = $a->field('tp_articles.id,a_title,image,a_man,a_laiyuan,content,cid,c.title,tag_ids,tp_articles.update_time')->where($where)->join('tp_category c','tp_articles.cid=c.c_id')->paginate(3,false,['query'=>$query]);//分页额外带的参数
        $this->assign('list',$list);
        return $this->fetch();
   }
   //删除
   public function del()
   {
        $id=input('get.id');
        $status = articles::destroy($id);
        if ($status) {
          $this->success('删除成功','list');
       }else{
          $this->error('删除失败');
       }
   }
   //修改查询
    public function update()
   {   
          //实例化分类category类
        $c = new category;
        $cate = $c->field('c_id,title')->all(); 
        $this->assign('cate',$cate);

        //实例化标签dao类
        $c = new dao;
        $dao = $c->field('q_id,shen')->all(); 
        $this->assign('dao',$dao);

        //实例化内容articles类
        $id=input('param.id');
        $a = new articles;
        $info=$a->get($id);
        $this->assign('info',$info);
        return $this->fetch();
   }
   //修改
   public function update_do()
   {
        $id=input('post.id');
       $data =input('post.');//接name值
       // var_dump($data);die;
    //-------=-----------------------------------------------------------
       //文件上传
       if (input('?file.image')) {
            $file = request()->file('image');//接收上传的name值
            $info = $file->move( './uploads');// 移动到框架应用根目录/uploads/ 目录下
            if($info){
                $data['image']= '/uploads/'.$info->getSaveName();
            }else{
                echo $file->getError();// 上传失败获取错误信息
            }
       }
    //-------------------------------------------------------------------------
    // 验证非空。。。。。。。
        $k = new Articlex;
        if (!$k->check($data)) {
           $this->error($k->getError());
        }
    //--------------------------------------------------------------------------
       //实例化内容articles类// $data['tag_ids'] = implode(',',$data['tag_ids']);
       $a = new articles;
       $status = $a->save($data,['id'=>$id]);// var_dump($status);die;
       if ($status) {
          $this->success('修改成功','list');
       }else{
          $this->error('修改失败');
       }
   }
























}
