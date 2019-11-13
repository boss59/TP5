<?php

namespace app\admin\model;

use think\Model;
use think\model\concern\SoftDelete;//引入软删除
class articles extends Model
{
	use SoftDelete;//引入软删除
	protected $deleteTime = 'delete_time';//指定字段
	// protected $defaultSoftDelete = 0;//指定字段 ，如果是Null不需要设置
    public $pk = 'id';//主键id
    protected $autoWriteTimestamp = true;//开启时间
    //修改器  自动动拼接字符串
    public function setTagIdsAttr($value)
    {
    	return implode(',',$value);
    	//手动拼接字符串
    	//$data['tag_ids'] = implode(',',$data['tag_ids']);
    }
    //获取器
      public function getTagIdsAttr($value)
    {
    	//查标签表tag_ids字段
    	$tags=db()->name('dao')->where('q_id','in',$value)->column('shen');//指定一列，返回一维数组
    	return implode(',',$tags);
    }
}
