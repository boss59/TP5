<?php

namespace app\ecshop\model;

use think\Model;

class admin extends Model
{
    public $pk = 'cid';//主键id
    protected $autoWriteTimestamp = true;//开启时间
    //修改器 密码
    public function setUserpwdAttr($value)
    {
    	return md5($value);
    }
}
