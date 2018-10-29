<?php
namespace app\admin\controller;

use app\common\model\Index;
use app\common\service\AuthGroup;
use app\common\service\AuthGroupAccess;
use app\common\service\AuthRule;
use app\common\service\Users as UsersService;
use think\Validate;

use think\Controller;

class Roles extends Base
{
    public function _empty()
    {
        return $this->index();
    }

    public function index()
    {
        $arrS = (new AuthGroup())->getAllListsByWhere(
            [],
            ['id,title,status,rules,remark']
        );
        $this->assign('data', $arrS);
        $this->assign('count', count($arrS));
        return $this->fetch('index');
    }

    public function getRuleTree()
    {
        $rules = (new AuthRule())->getAllListsByWhere(['pid'=>0],['id,title,pid,name']);
        $data = [];
        foreach($rules as $k=>$v){
            //获取对应的二级栏目列表
            $data[$k] = $v->toArray();
            $data[$k]['sub'] = [];
            $subList = (new AuthRule())->getAllListsByWhere(['pid'=>$v['id']],['id,title,pid,name']);
            foreach($subList as $key=>$value){
                $value['title'] = ' '.$value['title'];
                $data[$k]['sub'][$key] = $value->toArray();
            }
        }
        return $data;
    }

    public function addRole()
    {
        if($this->request->isAjax()){
            $data['title'] = $this->request->post('title');
            $data['remark'] = $this->request->post('remark');
            $data['rules'] = isset($this->request->post()['rules']) ? implode(',', $this->request->post()['rules']) : '';
            $data['status'] = 1;
            $res = (new AuthGroup())->insert($data);//新增
            if($res){
                $this->success('新增成功', 'admin/roles/index');//成功跳转
            }else{
                $this->error('新增失败');//失败跳转
            }
        }else{
            //获取权限节点列表
            $rules = $this->getRuleTree();
            $this->assign('rules', $rules);
            return $this->fetch('add-role');
        }
    }

    public function editRole($id)
    {
        if ($this->request->isAjax()){
            $data['title'] = $this->request->post('title');
            $data['remark'] = $this->request->post('remark');
            $data['rules'] = isset($this->request->post()['rules']) ? implode(',', $this->request->post()['rules']) : '';
            $data['status'] = 1;
            $res = (new AuthGroup())
                ->where('id', $id)
                ->update($data);//新增
            if($res){
                $this->success('修改成功', 'admin/users/index');//成功跳转
            }else{
                $this->error('修改失败');//失败跳转
            }
        }else{
            $authGroupInfo = (new AuthGroup())->getOneByWhere(['id'=>$id],'id,title,status,rules,remark')->toArray();
            $authGroupInfo['rules'] = explode(',', $authGroupInfo['rules']);
//            var_dump('<pre>', $authGroupInfo);
            //获取权限节点列表
            $rules = $this->getRuleTree();
            $this->assign('rules', $rules);
            $this->assign('authGroupInfo', $authGroupInfo);
            return $this->fetch('edit-role');
        }
    }

    //删除单个(公共方法)
    public function del()
    {
        $model = $this->request->post('model');//数据模型类名
        $id = $this->request->post('id');
        //删除之前不能有其他绑定用户
        if((new \app\common\model\AuthGroupAccess())->where('group_id', $id)->count()){
            $this->error('用户组下存在其他用户不得删除');
        }
        //绑定了其他规则的用户组也不能进行删除
        $rulesInfo = \think\Loader::model($model)->where(['id'=>$id])->find($id);
        if($rulesInfo['rules']){
            $this->error('用户组下绑定了访问规则不得删除');
        }
        $res = \think\Loader::model($model)->where(['id'=>$id])->delete();
        if ($res) {
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
        //删除之前不能有其他绑定用户
        $uids = (new AuthGroupAccess())->getOneByWhere(['group_id'=>array('in', rtrim($id, ','))], ['uid,group_id']);
        if(count($uids) > 0){
            $this->error('用户组下存在其他用户不得删除');
        }
        $where['id'] = array('in', rtrim($id, ','));
        $res = \think\Loader::model($model)->where($where)->delete();
        if ($res) {
            $this->success('删除成功', 'admin/users/index');
        } else {
            $this->error('删除失败');
        }
    }

}
