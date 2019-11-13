<?php

namespace app\ecindex\controller;

use think\Controller;
use think\Request;
use app\ecindex\model\Coupons as CouponsModel;
use app\ecindex\model\Cou;
use app\ecindex\model\Good;
class Coupons extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // 优惠券
        $cou = CouponsModel::join('tp_car','tp_Coupons.cid=tp_car.cid')->select();
        // echo CouponsModel::getLastSql($cou);die;
        $this->assign('cou',$cou);
        return view();
    }
    // 优惠券
    public function pons()
    {
        $vid = input('param.vid');
        $coupons = input('param.coupons');
        $res  = $this->checkLogin();
        if (!$res) {
            echo json_encode(['ret'=>'0','msg'=>'请先登陆,是否进行']);die;
        }
        $where = [
            "r_id"=>session('indexinfo')['r_id'],
            "vid"=>$vid,
        ];
        // 启动事务
        Cou::startTrans();
        try {
            $data = Cou::where(["vid"=>$vid])->find();
            // dump($data);
            if (!$data) {
               $cou = Cou::create($where);
               if ($cou) {
                   CouponsModel::where(["vid"=>$vid])->dec('num')->update();
                   echo json_encode(['ret'=>'1','msg'=>'领取成功']);
               } 
            }else{
                $ass=Cou::where($where)->select();
                if ($ass) {
                    echo json_encode(['ret'=>'1','msg'=>'已经领取过，不能再领了哦！']);
                }
            }
         // 提交事务
        Cou::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Cou::rollback();
            // echo $e->getNessage();
        }
    }
   
}
