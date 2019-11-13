<?php

namespace app\ecshop\controller;

use think\Controller;
use think\Request;
use app\ecshop\model\Kass;// 模型
use app\ecshop\validate\S;// 验证器
class News extends Base
{
    public function add()
    {
        return $this->fetch();
    }
    // 添加
    public function add_do()
    {
        $data = input('post.');
        $k = new S;
        // 验证
        if (!$k->check($data)) {
            $this->error($k->getError());
        }
        // 添加
        $m = new Kass;
        $status = $m->save($data);
        // var_dump($status);die;
        if ($status) {
           $this->success('添加成功',url('News/list'));
        }else{
           $this->error('添加失败');
        }
        
    }
    // 查询
    public function list()
    {
        $m = new Kass;
        $page=config('pageSize');
        $list = $m->paginate($page);
        $this->assign('list',$list);
        return $this->fetch();
    }
    //删除
    public function del()
    {
        $id = input('param.id');
        $m = new Kass;
        $status = $m->destory($id);
        if ($status) {
            echo 1;die;
        }else{
            echo 0;die;
        }   
    }
    // 修改
    public function update()
    {
        $id = input('param.id');
        $m = new Kass;
        $info = $m->find($id);
        $this->assign('info',$info);
        return $this->fetch();
    }
    // 执行 修改
    public function update_do()
    {
        $data = input('post.');
        $id = input('param.id');
        // 验证
        $k = new S;
        if (!$k->scene('edit')->check($data)) {
            $this->error($k->getError());
        }
        // 添加
        $m = new Kass;
        $status = $m->save($data,['qid'=>$id]);
        if ($status) {
           $this->success('修改成功',url('News/list'));
        }else{
           $this->error('修改失败');
        }
    }
    // 唯一性
    public function paly()
    {
        $post = input('post.');
        $where[] = ['qname','=',$post['qname']];
        if(isset($post['qid'])){
            $where[] = ['qname','<>',$post['qname']];
        }
        $count = Kass::where($where)->count();
        echo json_encode(['code'=>'00000',"count"=>$count,'msg'=>'成功']);
    }
}
