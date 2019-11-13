<?php

namespace app\ecindex\model;

use think\Model;

class Region extends Model
{
    public $pk ='region_id';
    // 循环地址
    public static function getAddressName($region_id)
    {
       $name = self::where('region_id',$region_id)->value('region_name');
       // dump($name);die;
       return $name;
    }
}
