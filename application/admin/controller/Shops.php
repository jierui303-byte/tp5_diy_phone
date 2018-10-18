<?php
namespace app\admin\controller;

use app\common\model\Cate;
use app\common\service\AuthGroup;
use app\common\service\AuthGroupAccess;
use app\common\service\Users as UsersService;
use think\Request;
use think\Validate;

use think\Controller;

class Shops extends Base
{
    public function sort($cateRes, $pid=0, $level=0){
        static $arr=array();
        foreach ($cateRes as $k=>$v){
            if($v['pid'] == $pid){
                //顶级分类
                $v['level'] = $level;
                $arr[] = $v;
                $this->sort($cateRes, $v['id'], $level+1);//找到顶级id并作为父级id传进来，然后把level+1
            }
        }
        return $arr;
    }

    public function index()
    {
        $cates = \think\Loader::model('Cate')->select();
        $q = $this->sort($cates, 0, 0);
//        dump($q);

        echo '<select name="" id="">';
        foreach($q as $k=>$v){
            if($v['level'] == 0){
                echo '<option value="'.$v['id'].'">'.$v['cate_name'].'&&&&&'.$v['level'].'</option>';//顶级分类
            }elseif($v['level'] == 1){
                echo '<option value="'.$v['id'].'">===='.$v['cate_name'].'&&&&&'.$v['level'].'</option>';//顶级分类
            }elseif($v['level'] == 2){
                echo '<option value="'.$v['id'].'">二二二二二'.$v['cate_name'].'&&&&&'.$v['level'].'</option>';//二级或者三级甚至更多
            }elseif($v['level'] == 3){
                echo '<option value="'.$v['id'].'">ssanjisanjisanjisanjianji'.$v['cate_name'].'&&&&&'.$v['level'].'</option>';//二级或者三级甚至更多
            }elseif($v['level'] == 4){
                echo '<option value="'.$v['id'].'">aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'.$v['cate_name'].'&&&&&'.$v['level'].'</option>';//二级或者三级甚至更多
            }
        }
        echo '</select>';
//        dump($q);

        exit;
        $authGroups = (new AuthGroup())->getAllListsByWhere(
            [
                'id'=>2 //商铺管理员
            ],
            ['id,title,status,rules']
        );
        $authGroupsAccess = (new AuthGroupAccess())->getAllListsByWhere(
            [
                'group_id'=>2 //商铺管理员
            ],
            ['uid,group_id']
        );

        $uid = [];
        foreach($authGroupsAccess as $k=>$v){
            $uid[] = $v['uid'];
        }
        $uidStr = implode(",",$uid);
//        var_dump('<pre>', implode(",",$uid));exit;

        $arrS = (new UsersService())->getAllListsByWhere(
            [
                'uid' => array('in', $uidStr)
            ],
            ['uid,user_name,email,real_name,sex,date_of_birth,status,address,phone,update_time']
        );
//        var_dump('<pre>', $arrS);

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
            $data['password'] = md5($psd);
            $data['email'] = $this->request->post('email');
            $data['real_name'] = $this->request->post('real_name');
            $data['sex'] = $this->request->post('sex');
            $data['date_of_birth'] = $this->request->post('date_of_birth');
            $data['address'] = $this->request->post('address');
            $data['phone'] = $this->request->post('phone');
            $data['create_time'] = date('y-m-d h:i:s',time());
            $res = (new UsersService())->insert($data);//新增
            if($res){
                //把用户名和相应规则绑定在一起
                $a = (new AuthGroupAccess())->insert(array(
                    'uid' => (new UsersService())->getLastInsID(),
                    'group_id' =>$this->request->post('adminRole')
                ));
                if($a){
                    $this->success('新增成功', 'admin/users/index');//成功跳转
                }else{
                    $this->error('绑定用户和角色失败');//失败跳转
                }
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
            $data['password'] = md5($psd);
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
                $a = (new AuthGroupAccess())
                    ->where('uid', $uid)
                    ->update(array(
                        'uid' => $uid,
                        'group_id' =>$this->request->post('adminRole')
                    ));
                if($a){
                    $this->success('修改成功', 'admin/users/index');//成功跳转
                }else{
                    $this->error('绑定用户和角色失败');//失败跳转
                }
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
            $group_id = (new AuthGroupAccess())->getOneByWhere(['uid'=>$uid], ['group_id'])->toArray()['group_id'];
            $this->assign('authGroups', $authGroups);
            $this->assign('userInfo', $userInfo);
            $this->assign('group_id', $group_id);
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
            $a = \think\Loader::model($model2)->where(['uid'=>$uid])->delete();
            if($a){
                $this->success('删除成功', 'admin/users/index');
            }else{
                $this->error('删除用户和角色绑定失败');
            }
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