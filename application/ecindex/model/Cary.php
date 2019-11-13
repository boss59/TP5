<?php

namespace app\ecindex\model;

use think\Model;

class Cary extends Model
{
    public $pk='c_id';
    public static function getMoney($gid)
    {
    	$sql = "select SUM(goods_number*shop_price) as total from tp_cary where  gid in ($gid)";
    	$total = \Db::query($sql);
    	return $total[0]["total"]??0;
    }
     public static function getMoneys($gid)
    {
    	$sql = "select SUM(goods_number*shop_price) as total from tp_cary where r_id=$gid";
    	$total = \Db::query($sql);
    	return $total[0]["total"]??0;
    }
}
