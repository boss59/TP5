<?php

namespace app\admin\model;

use think\Model;

class news extends Model
{
	public $pk='n_id';
	protected $autoWriteTimestamp = true;//开启时间
	public function getNumattr($value)
	{
		$is=['否','是'];
		return $is[$value];
	}
}
