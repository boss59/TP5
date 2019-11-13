<?php

namespace app\admin\validate;

use think\Validate;

class Articlex extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        //require 飞空
       'a_title' => ['require','max:20'],
       'cid' => ['require','number','gt:0'],
       'content' => ['require','max:30'],
       'tag_ids' => ['array'],
       'a_man' => ['require','max:30'],
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'a_title.require' =>'标题必填',
        'a_title.max' =>'标题长度不可超过20！',
        'content.require' =>'内容必填',
        'content.max' =>'内容长度不可超过30！',
        'cid.require' =>'分类必填！',
        'cid.number' =>'分类ID必须是数字！',
        'cid.gt' =>'请选择正确的分类！',
        'tag_ids.array' =>'标签提交有误！',
        'a_man.require' =>'作者必填！',
        'a_man.max' =>'作者长度不可超过30',
    ];
}
