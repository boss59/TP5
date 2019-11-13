<?php

namespace app\admin\model;

use think\Model;

class admin extends Model
{
    public $pk = 'a_id';//主键id
    protected $autoWriteTimestamp = true;//开启时间
    //修改器 密码
    public function setUserpwdAttr($value)
    {
    	return md5($value);
    }
}
