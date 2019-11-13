<?php

namespace app\ecindex\controller;

use think\Controller;
use think\Request;
use think\captcha\Captcha;//引入 验证码
use app\ecindex\model\Regist as RegistModel;
use app\ecindex\validate\V;
class Regist extends Base
{
    public function regist()
    {
        $this->view->engine->layout(false);
        return $this->fetch();
    }
    //注册
    public function add_do()
    {
        $data = input('post.');
        // 验证码
        // if(!captcha_check($data['code']))
        // {
        //     $this->error('验证码不正确');
        // }
        // 验证
        // $k = new V;
        // if (!$k->check($data)) {
        //     $this->error($k->getError());
        // }
        // 激活码 唯一标识
        $token= md5($data['user'].$data['pwd'].time());
        $data['token'] = $token;
        //设置过期时间 
        $rtime = time()+360;
        $data['rtime'] = $rtime;
        // 注册
        $m = new RegistModel;
        $name = $data['user'];
        $cc = $m->where('user',$name)->find();
        if ($cc) {
            $this->error('用户名已存在');
        }
        $status = $m->save($data);
        // 邮箱
        $email = $data['email'];
        $sjr=$email;
        $title='百步飞剑';
        $url = "http://w3.xxoo.cn/ecindex/regist/changestatus?token=".$token;
        $content="欢迎来到xxoo网站，在这里将对你的账号进行激活，请到<a href='{$url}'>'$url'</a>";
        $result=sendEmail($email,$title,$content);  //调用common.php的方法发送邮件
        if ($result) {
            $this->success("注册成功,请前往激活");
        }
        // var_dump($status);die;
        // if ($status) {
        //     $return = ['ret'=>1,'msg'=>'添加成功'];
        // }else{
        //     $return = ['ret'=>0,'msg'=>'添加失败'];
        // }
        // echo json_encode($return);die;
    }
    //激活
    public function changestatus()
    {
        $token = input('param.token');
        $status =RegistModel::where(['token'=>$token])->find();// 查一条数据
        // var_dump($data);die;
        if ($status['rtime'] > time()) {
            //没过期
            $res=RegistModel::where(['token'=>$token])->update(['status'=>1]);
            $this->error('激活成功');
        }else{
            $this->error('请重新注册',url("ecindex/regist/regist"));
        }
    }
}
