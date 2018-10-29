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
        if ($this->request->isAjax()){
            $data['user_name'] = $this->request->post('user_name');
            $data['admin_role_id'] = $this->request->post('admin_role_id');
            $data['email'] = $this->request->post('email');
            $data['real_name'] = $this->request->post('real_name');
            $data['sex'] = $this->request->post('sex');
            $data['date_of_birth'] = $this->request->post('date_of_birth');
            $data['address'] = $this->request->post('address');
            $data['phone'] = $this->request->post('phone');
            $res = (new UsersService())
                ->where('uid', $uid)
                ->update($data);//新增
            if($res){
                //把用户名和相应规则绑定在一起
               (new AuthGroupAccess())
                    ->where('uid', $uid)
                    ->update(array(
                        'uid' => $uid,
                        'group_id' =>$this->request->post('adminRole')
                    ));
                $this->success('修改成功', 'admin/users/index');//成功跳转
            }else{
                $this->error('修改失败');//失败跳转
            }
        }else{
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
    }

    //修改用户密码
    public function updateUserPassword()
    {

        return $this->fetch('update-password');
    }

}
