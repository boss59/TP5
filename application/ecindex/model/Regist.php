<?php

namespace app\ecindex\model;

use think\Model;

class Regist extends Model
{
    public $pk = 'r_id';
    protected $autoWriteTimestamp = true;
    public function setPwdAttr($value)
	{
		return md5($value);
	}
	public function setSpwdAttr($value)
	{
		return md5($value);
	}
}
