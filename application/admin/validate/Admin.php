<?php

namespace app\admin\validate;

use think\Validate;

class Admin extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
            'username' => ['require','chsDash','max:20',],
            'userpwd' => ['require','number','max:10',],
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'username.require' =>'用户名必填',
        'username.chsDash' =>'用户名只能是汉字、字母、数字和下划线_及破折号-',
        'username.max' =>'长度不可超过20',
        'userpwd.require' =>'密码必填',
        'userpwd.number' =>'密码只能数字',
        'userpwd.max' =>'长度不可超过10',
    ];
}
