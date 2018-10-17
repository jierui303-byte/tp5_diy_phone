<?php
namespace app\common\model;

use think\Db;
use think\Model;
use traits\model\SoftDelete;

class MaskPicture extends Model
{
    use SoftDelete;//要使用软删除功能，需要引入SoftDelete trait
    protected $deleteTime = 'delete_time';//定义软删除时间戳字段名
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $table = 'diy_mask_picture';//【带表前缀】设置当前模型对应的完整数据表名称
    protected $name = "mask_picture";
    protected $pk = 'id';//定义主键
    protected $autoWriteTimestamp = 'datetime';//如果你的时间字段不是int类型的话，例如使用datetime类型的话，可以这样设置：
//    protected $autoWriteTimestamp = true;//开启自动写入时间戳字段 默认识别为整型int类型


    //给表字段设置类型自动转换，会在写入和读取的时候自动进行类型转换处理
    protected $type = [
        'cate_id'     =>  'integer',
        'pic_old_name'     =>  'require',
        'pic_path'     =>  'require',
        'status'    =>  'integer',
    ];

    //在model中配置字段验证规则,在整个model中新增和更新操作都通用。
    protected $validate = [
        'rule' => [
            'cate_id' => 'integer',
            'pic_old_name' => 'require',
            'pic_path' => 'require',
            'status'    =>  'integer',
        ],
        'msg' => [
            'cate_id' => '分类ID不能为空',
            'pic_old_name' => '图片原名不能为空',
            'pic_path' => '图片服务器地址不能空',
            'status'    =>  'status状态值不能为空',
        ]
    ];

}