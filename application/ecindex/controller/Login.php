<?php

namespace app\ecindex\controller;

use think\Controller;
use think\Request;
use app\ecindex\model\Regist as RegistModel;// 表
use think\facade\Session;//引入  Session
use think\facade\Cookie;//引入 Cookie
use app\ecindex\model\History;
use app\ecindex\model\Cary;
use app\ecindex\model\Good;
class Login extends Base
{
	// 登陆页
    public function login()
    {
        $refer = input('refer');
    	$this->view->engine->layout(false);
        $this->assign('refer',$refer);
        return $this->fetch();
    }
    // 登陆
    public function login_do()
    {
    	$remember = input('post.remember');
        $refer = input('post.refer');
    	// var_dump($remember);die;
       	$data = input('post.');
       	$m = new RegistModel;
       	$info = $m->where('user',$data['user'])->find()->toArray();////判断 用户 是否正确
       	if ($info) {
		   	// 如果 账号 是错误 锁定状态 error_num =3
		   		if ($info['error_num'] == 3) {
		   			if ($info['error_time'] > time()) {
		   				//没过期
		   				$this->error('密码错误都次,账号已锁定到'.date("Y-m-d H:i:s",$info['error_time']));die;
		   			}else{
		   				// 过期
		   				RegistModel::where(['r_id'=>$info['r_id']])->update(['error_time'=>0,'error_num'=>0]);
		   			}
		   		}
            //判断密码是否正确
	        if ($info['pwd']==md5($data['pwd'])) {//用库里加密密码== 接收的加密密
	        	// ------------------------记录日志----------------------------
	   			// 查询 数据库 比对数据 与上次登陆进行比对
	   			$logdata = \Db::table('tp_login')->where('r_id',$info['r_id'])->order('log_id','desc')->find();
	           	if ($logdata && $logdata['log_ip'] != $_SERVER['REMOTE_ADDR']) {
	           		// 上次访问ip 和本次ip不一致
	           		// 发送邮件提醒 
	           		$email=$info['email'];
			        $title='异地登陆';
			        $content="您的账号在异地登陆,";
			        $result=sendEmail($email,$title,$content);  //调用common.php的方法发送邮件
	           	}
	           	// 记录日志 添加入库
	   			$datas = [
	          		'log_time' => time(),
	          		'log_ip' => $_SERVER['REMOTE_ADDR'],
	          		'r_id' => $info['r_id'],
	          	];
	        	\Db::name('login')->insert($datas);
	        	//-----------------------------------------------------
	        	session::set('indexinfo',$info);//记录session  看用户是否是登陆状态
	        	$this->asyncHistory();
                $this->asynccar();
                // 跳转
                if ($refer) {
                    $this->redirect($refer);
                }else{
                    $this->success('登陆成功！',url('index/index'));
                }
	        }else{
	        // -------------------记录错误时间--------------------------------------
	        	if ($info['error_num'] == 2) {
	        		// 错误数 到2时 下次为3 记录错误时间
	        		$error_time = time()+180;
	        		RegistModel::where(['r_id'=>$info['r_id']])->inc('error_num')->update(['error_time'=>$error_time]);
	        	}else{
	        		RegistModel::where(['r_id'=>$info['r_id']])->inc('error_num')->update();
	        	}
	            $this->error('密码不正确,三次后锁定半小时');
	        }
        }else{
            $this->error('用户不正确');
        }
    }
    /**
     *
     *退出 登陆
     * 
    */
    public function Logour()
    {
    	session('indexinfo',Null);
    	$this->redirect('Index/index');
    }
    // 同步
    public function asyncHistory()
    {
    	$history = json_decode(cookie('history'),true)?:[];
    	// dump($history);die;
    	if (!count($history)) {
    		return;
    	}
    	foreach ($history as $key => $value) {
    		$where = ['gid'=>$value['gid'],'r_id'=>session('indexinfo')['r_id']];
    		$data = History::where($where)->find();
    		// dump($data);die;
    		if ($data) {
    			// 数据库存在此条浏览记录
    			if ($value['create_time']>$data['create_time']) {
    				History::where($where)->update(['create_time'=>$value['create_time']]);
    			}
    		}else{
    			// 新增
    			$data = array_merge($where,['create_time'=>$value['create_time']]);
    			History::create($data);
    		} 
    	}
        cookie("history",null);
        
    }
    // 购物车
    public function asynccar()
    {
        $car = json_decode(cookie('car'),true)?:[];
        // dump($car);die;
        if (!count($car)) {
            return;
        }
        foreach ($car as $key => $value) {
            $where = [
                'r_id'=>session('indexinfo')['r_id'],
                'gid'=>$value['gid'],
            ];
            $data = Cary::where($where)->find();
            // dump($data);die;
            if ($data) {
                $value['goods_number'] += $data['goods_number'];
                $goods_number = Good::where('gid',$value['gid'])->value("goods_number");
                // dump($goods_number);die;
                if ($data['goods_number'] >$goods_number) {
                    $data['goods_number']  = $goods_number;
                }
               Cary::where($where)->update($data->toArray());
            }else{
                // 新增
                $goods = Good::get($value['gid']);
                // dump($goods);die;
                if ($goods['goods_number'] < $value['goods_number']) {
                    $value['goods_number']  = $goods['goods_number'];
                }
                $array = [
                    'r_id'=>session('indexinfo')['r_id'],
                    'gid'=>$value['gid'],
                    'goods_number'=>$value['goods_number'],
                    'shop_price'=>$value['shop_price'],
                    'goods_img'=>$value['goods_img'],
                    'goods_name'=>$value['goods_name'],
                    'create_time'=>time()
                ];
                Cary::create($array);
            }
        }
        cookie("car",null);
    }
}
