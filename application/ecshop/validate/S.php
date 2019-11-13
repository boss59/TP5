<?php

namespace app\ecshop\validate;

use think\Validate;

class S extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
            'qname' =>'require|chsDash|max:25|unique:kass',
            'content' => 'require',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
            'qname.require' =>'角色名称不能空',
            'qname.chsDash' =>'角色名称只能是汉字、字母、数字和下划线_及破折号-',
            'qname.max' =>'角色名称最大不超过25个字符',
            'qname.unique' =>'角色名称已存在',
            'content.require' =>'角色描述不能空',
    ];
    // edit 验证场景定义
    public function sceneEdit()
    {
        return $this->remove('qname','unique');
    }
}
