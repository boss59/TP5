<?php

namespace app\ecshop\model;

use think\Model;
class Good extends Model
{
	protected  $pk = 'gid';
    protected $autoWriteTimestamp = true;
    // public function getBIdAttr($value)
    // {
    //     $brand =db()->name('brand')->where('b_id','in',$value)->column('brand_name');//指定一列，返回一维数组
    //     return implode(',',$brand);
    // }
}
