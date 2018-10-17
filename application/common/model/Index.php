<?php
namespace app\common\model;

use think\Db;
use think\Model;
use traits\model\SoftDelete;

class Index extends Model
{
    protected $table = 'diy_index';//【带表前缀】设置当前模型对应的完整数据表名称
    protected $pk = 'id';//定义主键
    protected $autoWriteTimestamp = true;//设置时间格式
}