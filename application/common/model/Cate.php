<?php

namespace app\common\model;

use think\Db;
use think\Model;

class Cate extends Model
{
    protected $table = 'diy_cate';//【带表前缀】设置当前模型对应的完整数据表名称
    protected $name = "cate";
    protected $pk = 'id';//定义主键
}
