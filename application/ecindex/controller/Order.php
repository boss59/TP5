<?php

namespace app\ecindex\controller;

use think\Controller;
use think\Request;
use app\ecindex\model\User_address;
use app\ecindex\model\Region;
use app\ecindex\model\Cary;
use app\ecindex\model\Order_info;
use app\ecindex\model\Order_goods;
use Db;
class Order extends Base
{
    /**
     * 显示资源列表  
     *
     * @return \think\Response
     */
    public function index()
    {
        $gid = input('ids','');
        if (!$gid) {
            $this->redirect('car/index');
        }
        $r_id = session('indexinfo')['r_id'];
        $user_address = User_address::where('r_id',$r_id)->select();
        // dump($user_address);die;
        if (!count($user_address)) {
            $url = url('Order/address').'?ids='.$gid;
            $this->redirect($url);
        }
        // 循环 地址
        foreach ($user_address as $k => $v) {
            $user_address[$k]['country'] = Region::getAddressName($v['country']);
            $user_address[$k]['province'] = Region::getAddressName($v['province']);
            $user_address[$k]['city'] = Region::getAddressName($v['city']);
            $user_address[$k]['district'] = Region::getAddressName($v['district']);
        }
        // dump($user_address);die;
        // 定购的商品
        $where = [
            ['r_id','=',$r_id],
            ['gid','in',$gid]
        ];
        $cary=Cary::where($where)->select();
        // 金额
        $total=Cary::getMoney($gid);

        // 模板变量
        $this->assign('cary',$cary);
        $this->assign('gid',$gid);
        $this->assign('total',$total);
        $this->assign('user_address',$user_address);
        return view();
    }
    /**
     * 结算 ajax请求的方法
     * @return [type] [description]
     */
    public function confirmorder()
    {
        $gid = input('post.gid');
        if (!$this->checkLogin()) {
            echo json_encode(['code'=>'1','msg'=>'未登录']);die;
        }else{
            echo json_encode(['code'=>'0','msg'=>'已登录']);die;
        }
    }
    /**
     * 
     * ajax 地址信息
    */
   public function address()
   {
        $gid = input('ids','');
        if (!$gid) {
            $this->redirect('car/index');
        }
        $region = Region::where('parent_id',0)->select();
        $this->assign('region',$region);
        $this->assign('gid',$gid);
        return view();
   }
   // 地址提交信息
   public function saveaddress()
   {
        $post = input('post.');
         // dump($post);die;
        $post['r_id'] = session('indexinfo')['r_id'];
        $res=User_address::create($post);
        // dump($res);die;
        if ($res) {
            $url = url('Order/index').'?ids='.$post['gid'];
            $this->redirect($url);
        }
    }
    /**
     * 确认订单
     * @return [type] [description]
     */
    public function order()
    {
        $post = input('post.');
        // dump($post);
        // 启动事务
        Db::startTrans();
        try {
            // 获取订单号
            $post['order_sn']=$this->createordersn();
            //  获取收货人信息
            $address = User_address::get($post['address_id']);
            // dump($address);
            $shippin_data = ["1"=>'申通快递','2'=>'城际快递','3'=>'邮局平邮'];
            $post['shipping_name'] = $shippin_data[$post['shipping_id']];

            $pay_data = ['1'=>'余额支付','2'=>'银行汇款/转账','3'=>'货到付款',"4"=>'支付宝'];
            $post['pay_name'] = $pay_data[$post['pay_id']];
            $r_id = session('indexinfo')['r_id'];
            $post['order_amount'] = $post['goods_amount']=Cary::getMoney($post['gid']);
            $post['add_time'] = time();
            $data = array_merge($address->toArray(),$post);
            // dump($data);die;
            $res = order_info::create($data);
            // dump($res);die;
            if($res){
                // 添加 订单商品表
                // echo $res['order_id'];
                $goods = explode(',',$post['gid']);
                // dump($goods);die;
                foreach ($goods as  $v) {
                    $where = [
                        'r_id'=>$r_id,
                        'gid'=>$v
                    ];
                    $car=Cary::where($where)->find();
                    // dump($car);die;
                    $car['order_id'] = $res['order_id'];
                    Order_goods::create($car->toArray());
                    Cary::where($where)->delete();
                }
            }
        $message = 0;
        // 提交事务
        Db::commit();
        } catch (\Exception $e) {
            $message =1;
            // 回滚事务
            Db::rollback();
        }
        if (!$message) {
            $this->redirect('order/submitorder',['order_id'=>$res['order_id']]);
        }else{
            $this->redirect('Car/index');
        }
        
   }
   // 提交 订单
   public function submitorder()
   {
        $order_id = input('order_id');
        // dump($order_id);die;
        $data = Order_info::field("shipping_name,pay_name,order_amount,order_sn")->where('order_id',$order_id)->find();
        // dump($data);die;
        $this->assign('data',$data);
        $this->assign('order_id',$order_id);
        return view();
    }
    // 订单号
    public function createordersn()
    {
        return "DNF".date("YmdHis").rand(1000,9999);
    }

    /**
     *
     * 同步支付
     * [add description]
     */
    public function alipay()
    {
        $order_id = input('order_id');
        if (!$order_id) {
            return;
        }
        $order = Order_info::field("order_amount,order_sn")->where('order_id',$order_id)->find();
        // dump($data); 
        
        $config = config('alipay.');
        $extend_path = \Env::get('root_path').'extend/';
        require_once $extend_path.'alipay/pagepay/service/AlipayTradeService.php';
        require_once $extend_path.'alipay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php';

        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = trim($order['order_sn']);
        //订单名称，必填
        $subject = "全职 高手 -- 君莫笑";
        //付款金额，必填
        $total_amount = trim($order['order_amount']);
        //商品描述，可空
        $body = "";


        //构造参数
        $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setOutTradeNo($out_trade_no);

        $aop = new \AlipayTradeService($config);

        /**
         * pagePay 电脑网站支付请求
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @param $return_url 同步跳转地址，公网可以访问
         * @param $notify_url 异步通知地址，公网可以访问
         * @return $response 支付宝返回的信息
        */
        $response = $aop->pagePay($payRequestBuilder,$config['return_url'],$config['notify_url']);

        //输出表单
        var_dump($response);
    }
    // 同步校验
    public function returnpay()
    {
        $config = config('alipay.');
        $extend_path = \Env::get('root_path').'extend/';
        require_once $extend_path.'alipay/pagepay/service/AlipayTradeService.php';

        $arr=$_GET;
        // dump($arr);die;
        $alipaySevice = new \AlipayTradeService($config); 
        $result = $alipaySevice->check($arr);

        if($result) {//验证成功
            //请在这里加上商户的业务逻辑程序代码
            
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

            //支付宝交易号
            $trade_no = htmlspecialchars($_GET['trade_no']);
            //商户订单号
            $out_trade_no = htmlspecialchars($_GET['out_trade_no']);
            //商户 订单号金额
            $total_amount = htmlspecialchars($_GET['total_amount']);
            //商户 id
            $seller_id = htmlspecialchars($_GET['seller_id']);
            //app id
            $app_id = htmlspecialchars($_GET['app_id']);
            

            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
            $where = [
                "order_sn"=>$out_trade_no,
                "order_amount"=>$total_amount
            ];
            $count = Order_info::where($where)->count();
            if (!$count) {
                $msg = "支付宝交易号：".$trade_no.'商户定单号:'.$out_trade_no."金额".$total_amount."与本站不符，请联系管理员";
                $this->error($msg,'Order/myorder');
            }

            // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
            if ($seller_id != config('alipay.seller_id')) {
                $msg = "支付宝交易号：".$trade_no.'商户id:'.$seller_id."与本站不符，请联系管理员";
                $this->error($msg,'Order/myorder');
            }
            // 4、验证app_id是否为该商户本身。
            if ($app_id != config('alipay.app_id')) {
                $msg = "支付宝交易号：".$trade_no.'appid:'.$app_id."与本站不符，请联系管理员";
                $this->error($msg,'Order/myorder');
            }
             
 
            // echo "验证成功<br />支付宝交易号：".$trade_no;
            $this->redirect('Order/myorder');
        
        }else {
            //验证失败
            echo "验证失败";
        }
    }

    // 我的订单
    public function myorder()
    {
        $r_id = session('indexinfo')['r_id'];
        $orderinfo = Order_info::where(['r_id'=>$r_id])->order('order_id','desc')->select();
        $this->assign('orderinfo',$orderinfo);
        return view();
    }

    /* 实际验证过程建议商户添加以下校验。
        1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        4、验证app_id是否为该商户本身。
    */
    public function notifypay()
    {
        $config = config('alipay.');
        $extend_path = \Env::get('root_path').'extend/';
        require_once $extend_path.'alipay/pagepay/service/AlipayTradeService.php';

        $arr=$_POST;
        // \Log::write('异步','notice');die;
        $alipaySevice = new \AlipayTradeService($config); 
        $alipaySevice->writeLog(var_export($_POST,true));
        // $result = $alipaySevice->check($arr);
        $result = true;
        
        if($result) {//验证成功
        
        //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
        
            //支付宝交易号
            $trade_no = htmlspecialchars($_POST['trade_no']);
            //商户订单号
            $out_trade_no = htmlspecialchars($_POST['out_trade_no']);
            //商户 订单号金额
            $total_amount = htmlspecialchars($_POST['total_amount']);
            //商户 id
            $seller_id = htmlspecialchars($_POST['seller_id']);
            //app id
            $app_id = htmlspecialchars($_POST['app_id']);

            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
            $where = [
                "order_sn"=>$out_trade_no,
                "order_amount"=>$total_amount
            ];
            $count = Order_info::where($where)->count();
            if (!$count) {
                $msg = "支付宝交易号：".$trade_no.'商户定单号:'.$out_trade_no."金额".$total_amount."与本站不符，请联系管理员";
                \Log::write($msg,'notice');
            }

            // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
            if ($seller_id != config('alipay.seller_id')) {
                $msg = "支付宝交易号：".$trade_no.'商户id:'.$seller_id."与本站不符，请联系管理员";
                \Log::write($msg,'notice');
            }
            // 4、验证app_id是否为该商户本身。
            if ($app_id != config('alipay.app_id')) {
                $msg = "支付宝交易号：".$trade_no.'appid:'.$app_id."与本站不符，请联系管理员";
                \Log::write($msg,'notice');
            }

        // 判断
        if($_POST['trade_status'] == 'TRADE_FINISHED') {
            //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序
                    
            //注意：
            //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
        }else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
            //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序            
            //注意：
            //付款完成后，支付宝系统发送该交易状态通知
        }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            echo "success"; //请不要修改或删除
        }else {
            //验证失败
            echo "fail";
        }
    }
}
