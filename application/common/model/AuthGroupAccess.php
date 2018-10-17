<?php
namespace app\common\model;

use think\Db;
use think\Model;
use traits\model\SoftDelete;

class AuthGroupAccess extends Model
{
    protected $table = 'diy_auth_group_access';//【带表前缀】设置当前模型对应的完整数据表名称
    protected $name = "auth_group_access";
    protected $pk = '';//定义主键
}