<?php

namespace app\ecindex\model;

use think\Model;

class History extends Model
{
    public $pk = 'h_id';
    protected $autoWriteTimestamp = true;
}
