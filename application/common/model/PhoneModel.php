<?php

namespace app\common\model;

use think\Model;

class PhoneModel extends Model
{
    protected $table = 'diy_phone_model';//【带表前缀】设置当前模型对应的完整数据表名称
    protected $name = "phone_model";
    protected $pk = 'id';//定义主键
    protected $autoWriteTimestamp = 'datetime';//如果你的时间字段不是int类型的话，例如使用datetime类型的话，可以这样设置：
}
