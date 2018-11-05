<?php
namespace app\admin\controller;

use app\common\logic\Users;
use think\Controller;
use think\Session;

class Login extends Controller
{
    public function index()
    {
        if($this->request->isPost()){
            //登录判断
            $userInfo = (new Users())->getOneByWhere([
                'user_name' => $this->request->post('user_name'),
            ], ['uid,user_name,password']);
//            var_dump($userInfo);exit;
            if($userInfo){
                //如果用户名和密码都正确
                if(md5($this->request->post('password')) === $userInfo['password']){
                    //登录成功，存储UID
                    Session::set('uid', $userInfo['uid']);

                    //此时可以计算用户token 代表用户登录在线状态
                    $this->redirect('admin/order/index');
//                    $this->success('登录成功', 'admin/index/index');
                }else{
                    //用户密码不对
                    $this->error('用户密码不对');
                }
            }else{
                //用户不存在
                $this->error('用户不存在');
            }
        }else{
            return $this->fetch('login');
        }
    }

    public function getUserLoginToken()
    {
        //计算出token后，把token值存入数据库，代表该用户处于登录状态
        //当用户退出时，要把数据库中的token清空
        //一般token计算规则：用户登录设备，用户账号密码，干扰值，
        //使用hash加密计算
    }

    //退出
    public function quit_out()
    {
        Session::set('uid', null);
        if(!Session::get("uid")){
            $this->error("退出成功！", "admin/login/index");
        }
    }
}
