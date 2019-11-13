<?php

namespace app\ecshop\validate;

use think\Validate;

class V extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
            'username'=>'require',
            'userpwd'=>'require',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
            'username.require' =>'用户名必填',
            'userpwd.require' =>'密码必填',
    ];
}
