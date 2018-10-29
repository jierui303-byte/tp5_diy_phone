<?php
namespace app\admin\controller;

use app\common\model\Index;
use app\common\service\AuthGroupAccess;
use app\common\service\AuthRule;
use app\common\service\Users as UsersService;
use think\Validate;

use think\Controller;

class Permissions extends Base
{
    public function index()
    {
        $data = $this->getRuleTree();
        $this->assign('data', $data);
        $this->assign('count', count($data));
        return $this->fetch('index');
    }

    public function addPermission()
    {
        if($this->request->isAjax()){
            $data['model_name'] = $this->request->post('model_name');
            $data['controller_name'] = $this->request->post('controller_name');
            $data['action_name'] = $this->request->post('action_name');
            $data['pid'] = $this->request->post('pid');
            $data['post_type'] = $this->request->post('post_type');
            $data['title'] = $this->request->post('title');
            $data['condition'] = 1;
            $data['name'] = $this->request->post('model_name').'/'.$this->request->post('controller_name').'/'.$this->request->post('action_name');
            $res = (new AuthRule())->insert($data);//新增
            if($res){
                $this->success('新增成功', 'admin/permissions/index');//成功跳转
            }else{
                $this->error('新增失败');//失败跳转
            }
        }else{
            $rules = (new AuthRule())->getAllListsByWhere(['pid'=>0],['id,title,pid']);
            $data = [];
            foreach($rules as $k=>$v){
                //获取对应的二级栏目列表
                $data[] = $v->toArray();
                $subList = (new AuthRule())->getAllListsByWhere(['pid'=>$v['id']],['id,title,pid']);
                foreach($subList as $key=>$value){
                    $value['title'] = '---|'.$value['title'];
                    $data[] = $value->toArray();
                }
            }
            $this->assign('rules', $data);
            return $this->fetch('add-permission');
        }
    }

    public function editPermission($id)
    {
        if ($this->request->isAjax()){
            $data['model_name'] = $this->request->post('model_name');
            $data['controller_name'] = $this->request->post('controller_name');
            $data['action_name'] = $this->request->post('action_name');
            $data['pid'] = $this->request->post('pid');
            $data['post_type'] = $this->request->post('post_type');
            $data['title'] = $this->request->post('title');
            $data['name'] = $this->request->post('model_name').'/'.$this->request->post('controller_name').'/'.$this->request->post('action_name');
            $res = (new AuthRule())
                ->where('id', $id)
                ->update($data);//新增
            if($res){
                $this->success('修改成功', 'admin/permissions/index');//成功跳转
            }else{
                $this->error('修改失败');//失败跳转
            }
        }else{
            $ruleInfo = (new AuthRule())->getOneByWhere(
                ['id'=>$id],
                ['id,title,pid,name,model_name,controller_name,action_name,post_type']
            )->toArray();
            $data = $this->getRuleTree();
            $this->assign('rules', $data);
            $this->assign('ruleInfo', $ruleInfo);
            return $this->fetch('edit-permission');
        }
    }

    public function getRuleTree()
    {
        $rules = (new AuthRule())->getAllListsByWhere(['pid'=>0],['id,title,pid,name']);
        $data = [];
        foreach($rules as $k=>$v){
            //获取对应的二级栏目列表
            $data[] = $v->toArray();
            $subList = (new AuthRule())->getAllListsByWhere(['pid'=>$v['id']],['id,title,pid,name']);
            foreach($subList as $key=>$value){
                $value['title'] = '---| '.$value['title'];
                $data[] = $value->toArray();
            }
        }
        return $data;
    }

    //删除单个(公共方法)
    public function del()
    {
        $model = $this->request->post('model');//数据模型类名
        $id = $this->request->post('id');
        //判断一下当前这个规则是顶级规则还是子规则   子规则可以直接删除   顶级规则不能直接删除
        $authRulesInfo = (new \app\common\model\AuthRule())->find('id', $id);
        if($authRulesInfo['pid'] == 0){
            //判断是否 存在子规则
            if((new \app\common\model\AuthRule())->where(array(
                'pid'=>$id
            ))->count()){
                $this->error('存在子规则，不能进行删除');
            }else{
                //不存在子规则 可以删除
                $res = \think\Loader::model($model)->where(['id'=>$id])->delete();
                if ($res) {
                    //删除也需要删除用户组绑定的数据
                    $authRules = (new \app\common\model\AuthGroup())->select();
                    foreach($authRules as $k=>$v){
                        //var_dump($v['rules'], '删除前');
                        //把字符串转换成数组，删除然后再转回字符串
                        $arr = explode(',', $v['rules']);
                        if(in_array($id, $arr)){
                            $array = array_diff($arr, [$id]);
                            $arr = implode(',', $array);
                            //var_dump($arr, '删除后');
                            //把删除后的数据更新到数据库
                            (new \app\common\model\AuthGroup())->where('id', $v['id'])->update(array(
                                'rules' => $arr
                            ));
                        }
                    }
                    $this->success('删除成功', 'admin/permissions/index');
                } else {
                    $this->error('删除失败');
                }
            }
        }else{
            //子规则 可以直接删除
            $res = \think\Loader::model($model)->where(['id'=>$id])->delete();
            if ($res) {
                //删除也需要删除用户组绑定的数据
                $authRules = (new \app\common\model\AuthGroup())->select();
                foreach($authRules as $k=>$v){
                    //var_dump($v['rules'], '删除前');
                    //把字符串转换成数组，删除然后再转回字符串
                    $arr = explode(',', $v['rules']);
                    if(in_array($id, $arr)){
                        $array = array_diff($arr, [$id]);
                        $arr = implode(',', $array);
                        //var_dump($arr, '删除后');
                        //把删除后的数据更新到数据库
                        (new \app\common\model\AuthGroup())->where('id', $v['id'])->update(array(
                            'rules' => $arr
                        ));
                    }
                }
                $this->success('删除成功', 'admin/permissions/index');
            } else {
                $this->error('删除失败');
            }
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
            $this->success('删除成功', 'admin/permissions/index');
        } else {
            $this->error('删除失败');
        }
    }

}
