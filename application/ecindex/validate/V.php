<?php

namespace app\ecindex\validate;

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
            'user' => 'require|unique:tp_redist',
            'pwd' => 'require',
            'spwd' => 'require',
            'email' => 'email|require',
            'tel' => 'tel|require',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
            'user.require' => '名称不能空',
            'user.tp_redist' => '名称重复',
            'pwd.require' => '密码不能空',
            'spwd.require' => '确认密码不能空',
            'email.require' => '邮箱不能空',
            'tel.require' => '电话不能空',
            'email.email' => '请输入有效的邮箱',
            'tel.tel' => '请输入有效的电话',
    ];
}
