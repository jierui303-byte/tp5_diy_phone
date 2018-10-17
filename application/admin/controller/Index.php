<?php
namespace app\admin\controller;

use app\common\model\User;
use app\common\model\Index as Indexs;
use think\Validate;

use think\Controller;

class Index extends Base
{
    public function index()
    {

        return $this->fetch('index');
    }

    public function showError()
    {
        echo '您没有操作权限';
    }

}
