<?php
namespace app\common\model;

use think\Db;
use think\Model;

class AuthRule extends Model
{
    protected $table = 'diy_auth_rule';//【带表前缀】设置当前模型对应的完整数据表名称
    protected $name = "auth_rule";
    protected $pk = 'id';//定义主键
}