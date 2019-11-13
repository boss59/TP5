<?php

namespace app\ecshop\model;

use think\Model;

class Car extends Model
{
    public $pk = 'cid';//主键id
    protected $autoWriteTimestamp = true;//时间 戳
    // 修改器  自动动拼接字符串
    // public function setCatRecommendAttr($value)
    // {
    // 	return implode(',',$value);
    // 	//手动拼接字符串
    // 	//$data['tag_ids'] = implode(',',$data['tag_ids']);
    // }
}
