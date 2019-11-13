<?php

namespace app\ecindex\model;

use think\Model;

class Good extends Model
{
    protected  $pk = 'gid';
    protected $autoWriteTimestamp = true;
    // 楼层
    public static function getGoodsBywhere($where)
    {
    	return self::where($where)->select()->toArray();
    }
}
