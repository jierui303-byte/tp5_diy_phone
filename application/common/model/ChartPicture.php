<?php

namespace app\common\model;

use think\Db;
use think\Model;
use traits\model\SoftDelete;

class ChartPicture extends Model
{
    use SoftDelete;//要使用软删除功能，需要引入SoftDelete trait
    protected $deleteTime = 'delete_time';//定义软删除时间戳字段名
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $table = 'diy_chart_picture';//【带表前缀】设置当前模型对应的完整数据表名称
    protected $name = "chart_picture";
    protected $pk = 'id';//定义主键
    protected $autoWriteTimestamp = 'datetime';//如果你的时间字段不是int类型的话，例如使用datetime类型的话，可以这样设置：
//    protected $autoWriteTimestamp = true;//开启自动写入时间戳字段 默认识别为整型int类型
}
