<?php
namespace app\admin\controller;

use app\common\model\Index;
use app\common\service\AuthGroup;
use app\common\service\AuthGroupAccess;
use app\common\service\Users as UsersService;
use think\Validate;

use think\Controller;

class Users extends Base
{
    public function index()
    {
        $arrS = (new UsersService())->getAllListsByWhere(
            [],
            ['uid,user_name,email,real_name,sex,birth_time,status,address,phone,update_time,start_time,end_time']
        );
        //获取用户的角色名称
        foreach($arrS as $k=>$v){
            $groupId = (new AuthGroupAccess())->getOneById($v['uid'], ['group_id']);
//            var_dump('<pre>', $groupId['group_id']);
            if($groupId['group_id']){
                $authGroup = (new AuthGroup())->getOneById($groupId['group_id'], ['title']);
                $arrS[$k]['group_name'] = $authGroup['title'];
            }
        }

        $this->assign('data', $arrS);
        $this->assign('count', count($arrS));
        return $this->fetch('index');
    }

    public function addUser()
    {
        if($this->request->isAjax()){
            //判断密码是否一致
            $psd = $this->request->post('password');
            $psd2 = $this->request->post('password2');
            if($psd !== $psd2){
                $this->error('密码不一致');
            }
            $data['user_name'] = $this->request->post('user_name');
            $data['admin_role_id'] = $this->request->post('admin_role_id');
            $data['password'] = md5($psd);
            $data['email'] = $this->request->post('email');
            $data['real_name'] = $this->request->post('real_name');
            $data['sex'] = $this->request->post('sex');
            $data['date_of_birth'] = $this->request->post('date_of_birth');
            $data['address'] = $this->request->post('address');
            $data['phone'] = $this->request->post('phone');
            $data['start_time'] = $this->request->post('start_time');
            $data['end_time'] = $this->request->post('end_time');
            $data['birth_time'] = $this->request->post('birth_time');
            $data['create_time'] = date('y-m-d h:i:s',time());
            $res = (new UsersService())->insert($data);//新增
            if($res){
                //把用户名和相应规则绑定在一起
                (new AuthGroupAccess())->insert(array(
                    'uid' => (new UsersService())->getLastInsID(),
                    'group_id' =>$this->request->post('adminRole')
                ));
                $this->success('新增成功', 'admin/users/index');//成功跳转
            }else{
                $this->error('新增失败');//失败跳转
            }

        }else{
            //获取角色列表
            $authGroups = (new AuthGroup())->getAllListsByWhere(
                [],
                ['id,title,status']
            );
            $this->assign('authGroups', $authGroups);
            return $this->fetch('add-user');
        }
    }

    public function editUser($uid)
    {
        if ($this->request->isAjax()){
            //判断密码是否一致
            $psd = $this->request->post('password');
            $psd2 = $this->request->post('password2');
            if($psd !== $psd2){
                $this->error('密码不一致');
            }
            $data['user_name'] = $this->request->post('user_name');
            $data['admin_role_id'] = $this->request->post('admin_role_id');
            $data['password'] = md5($psd);
            $data['email'] = $this->request->post('email');
            $data['real_name'] = $this->request->post('real_name');
            $data['sex'] = $this->request->post('sex');
            $data['birth_time'] = $this->request->post('birth_time');
            $data['address'] = $this->request->post('address');
            $data['phone'] = $this->request->post('phone');
            $data['start_time'] = $this->request->post('start_time');
            $data['end_time'] = $this->request->post('end_time');
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
            $userInfo = (new UsersService())->getOneByWhere(['uid'=>$uid],'uid,user_name,email,real_name,sex,birth_time,address,phone,update_time,start_time,end_time');
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

    //删除单个(公共方法)
    public function del()
    {
        $model = $this->request->post('model');//数据模型类名
        $model2 = $this->request->post('mode2');//数据模型类名
        $uid = $this->request->post('id');
        $res = \think\Loader::model($model)->where(['uid'=>$uid])->delete();
        if ($res) {
            \think\Loader::model($model2)->where(['uid'=>$uid])->delete();
            $this->success('删除成功', 'admin/users/index');
        } else {
            $this->error('删除失败');
        }
    }

    //删除多个(公共方法)
    public function del_all()
    {
        $model = $this->request->post('model');//数据模型类名
        $id = $this->request->post('id');
        $where['uid'] = array('in', rtrim($id, ','));
        $res = \think\Loader::model($model)->where($where)->delete();
        if ($res) {
            $this->success('删除成功', 'admin/users/index');
        } else {
            $this->error('删除失败');
        }
    }

    public function admin_stop()
    {
        $model = $this->request->post('model');//数据模型类名
        $uid = $this->request->post('id');
        $status = $this->request->post('status');
        //判断是禁用还是启用
        if($status == 1){
            //执行禁用
            $res = \think\Loader::model($model)->where('uid', $uid)->update([
                'status' => 0
            ]);
            if ($res) {
                $this->success('禁用成功', 'admin/users/index');
            } else {
                $this->error('禁用失败');
            }
        }else{
            //执行启用
            $res = \think\Loader::model($model)->where('uid', $uid)->update([
                'status' => 1
            ]);
            if ($res) {
                $this->success('启用成功', 'admin/users/index');
            } else {
                $this->error('启用失败');
            }
        }
    }
}
