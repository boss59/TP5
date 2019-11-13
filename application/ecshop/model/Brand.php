<?php

namespace app\ecshop\model;

use think\Model;

class Brand extends Model
{
    public $pk = "b_id";
    protected $autoWriteTimestamp = true;
    //获取器
 //    public function getIsShowAttr($value)
	// {
	// 	$is = ['×','√'];
	// 	return $is[$value];
	// }

}
