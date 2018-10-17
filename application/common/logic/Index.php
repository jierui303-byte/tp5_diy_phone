<?php
namespace app\common\logic;

use think\Db;
use think\Model;

class Index extends Model
{
    //实例化方法：\think\Loader::model('User','logic');
    public function getLists()
    {
        $data = \think\Loader::model('Index')->where('id', 1)
            ->find()->toArray();
        return $data;
    }
}