<?php

namespace app\admin\model;

use think\Model;

class category extends Model
{
	//模型名字要与表名相同
    public $pk='c_id';//当前表主键id名
    protected $autoWriteTimestamp = true;//开启自动时间戳
    public function getIsNavattr($value)
    {
    	$is=['否','是'];
    	return $is[$value];
    }
}
