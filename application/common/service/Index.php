<?php
namespace app\common\service;

use think\Db;
use think\Model;

class Index extends Model
{
    //实例化方法：\think\Loader::model('User','service');
    public function getLists()
    {
        $indexLogic = \think\Loader::model('Index','logic');
        return $indexLogic->getLists();
    }

}