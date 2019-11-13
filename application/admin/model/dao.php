<?php

namespace app\admin\model;

use think\Model;

class dao extends Model
{
	//模型名字要与表名相同
    public $pk='q_id';//当前表主键id名
    protected $autoWriteTimestamp = true;//开启时间
}
