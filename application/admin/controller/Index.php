<?php
namespace app\admin\controller;

use app\common\service\AuthGroup;
use app\common\service\AuthGroupAccess;
use app\common\service\Users as UsersService;
use think\Session;
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

    //查看用户信息
    public function showUserInfo()
    {
        $uid = Session::get('uid');

        $userInfo = (new UsersService())->getOneByWhere(['uid'=>$uid],'uid,user_name,email,real_name,sex,date_of_birth,address,phone,update_time');
        //获取角色列表
        $authGroups = (new AuthGroup())->getAllListsByWhere(
            [],
            ['id,title,status']
        );
        //获取用户所属角色id
        $group_id = (new AuthGroupAccess())->getOneByWhere(['uid'=>$uid], ['group_id']);

        $this->assign('authGroups', $authGroups);
        $this->assign('userInfo', $userInfo);
        $this->assign('group_id', $group_id['group_id']);
        return $this->fetch('edit-user');
    }

    //修改用户密码
    public function updateUserPassword()
    {
        return $this->fetch('update-password');
    }

}
