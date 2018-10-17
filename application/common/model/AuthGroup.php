<?php
namespace app\common\model;

use think\Db;
use think\Model;

class AuthGroup extends Model
{
    protected $table = 'diy_auth_group';//【带表前缀】设置当前模型对应的完整数据表名称
    protected $name = "auth_group";
    protected $pk = 'id';//定义主键
}