<?php
namespace app\admin\controller;

use app\common\model\Users;
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
            $data['shop_name'] = $this->request->post('shop_name');
            $data['admin_role_id'] = $this->request->post('admin_role_id');
            $data['email'] = $this->request->post('email');
            $data['real_name'] = $this->request->post('real_name');
            $data['sex'] = $this->request->post('sex');
            $data['birth_time'] = $this->request->post('birth_time');
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
            $userInfo = (new UsersService())->getOneByWhere(['uid'=>$uid],'uid,user_name,shop_name,email,real_name,sex,birth_time,address,phone,update_time');
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
        $uid = Session::get('uid');
        if ($this->request->isAjax()){
            $oldPsd = $this->request->post('required:true,');//旧密码
            $psd = $this->request->post('password');//新密码
            $psd2 = $this->request->post('password2');//确认密码
            //判断旧密码是否正确，旧密码正确再进行新密码修改
            $userInfo = (new Users())->find($uid);
            if(md5($oldPsd) === $userInfo['password']){
                //旧密码校验正确 允许修改新密码
                if($psd !== $psd2){
                    $this->error('密码不一致');
                }
                $data['password'] = md5($psd);
                $res = (new UsersService())
                    ->where('uid', $uid)
                    ->update($data);//新增
                if($res){
                    $this->success('密码修改成功', 'admin/index/index');//成功跳转
                }else{
                    $this->error('密码修改失败');//失败跳转
                }
            }else{
                //用户密码不对
                $this->error('用户旧密码不对');
            }
        }else{
            $userInfo = (new UsersService())->getOneByWhere(['uid'=>$uid],'uid,user_name,email,real_name,sex,birth_time,address,phone,update_time');

            $this->assign('userInfo', $userInfo);
            return $this->fetch('update-password');
        }
    }

}
