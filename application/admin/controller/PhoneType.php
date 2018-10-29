<?php

namespace app\admin\controller;

use app\common\model\PhoneVarieties;
use app\common\service\PhoneType as PhoneTypeService;
use think\Request;
use think\Validate;
use think\Db;

use think\Controller;

class PhoneType extends Base
{
    public function index($brand_id)
    {
        $arrS = (new PhoneTypeService())->getAllListsByWhere(
            [
                'status' => 1,
                'brand_id' => $brand_id
            ],
            ['id,type_name,brand_id,update_time']
        );
        $this->assign('data', $arrS);
        $this->assign('count', count($arrS));
        $this->assign('brand_id', $brand_id);
        return $this->fetch('index');
    }

    public function addPhoneType($brand_id)
    {
        if ($this->request->isAjax()){
            $data['type_name'] = $this->request->post('type_name');
            $data['brand_id'] = $this->request->post('brand_id');
            $data['create_time'] = date('y-m-d h:i:s',time());
            $res = (new PhoneTypeService())->insert($data);//新增
            if($res){
                $this->success('新增成功', 'admin/PhoneType/index');//成功跳转
            }else{
                $this->error('新增失败');//失败跳转
            }
        }else{
            $token = $this->request->token('__token__', 'sha1');//生成token 自定义令牌生成规则，可以调用Request类的token方法
            $this->assign('token', $token);
            $this->assign('brand_id', $brand_id);
            return $this->fetch('add-phone-type');
        }
    }

    public function editPhoneType($id)
    {
        if ($this->request->isAjax()){
            $data['type_name'] = $this->request->post('type_name');
            $data['brand_id'] = $this->request->post('brand_id');
            $res = (new PhoneTypeService())
                ->where('id', $id)
                ->update($data);//更新
            if($res){
                $this->success('修改成功', 'admin/PhoneType/index');//成功跳转
            }else{
                $this->error('修改失败');//失败跳转
            }
        }else{
            //获取分类信息
            $res = (new PhoneTypeService())->getOneById($id, ['id,type_name,brand_id']);
            $token = $this->request->token('__token__', 'sha1');//生成token 自定义令牌生成规则，可以调用Request类的token方法
            $this->assign('token', $token);
            $this->assign('data', $res);
            return $this->fetch('edit-phone-type');
        }
    }

    //删除单个(公共方法)
    public function del()
    {
        $model = $this->request->post('model');//数据模型类名
        $id = $this->request->post('id');
        //删除之前先查询当前机型下是否存在品种数据
        if((new PhoneVarieties())->where('type_id', $id)->count()){
            $this->error('该机型下存在品种数据，不允许删除');
        }
        $res = \think\Loader::model($model)->where(['id'=>$id])->delete();
        if ($res) {
            $this->success('删除成功', 'admin/PhoneType/index');
        } else {
            $this->error('删除失败');
        }
    }

    //删除多个(公共方法)
    public function del_all()
    {
        $model = $this->request->post('model');//数据模型类名
        $id = $this->request->post('id');
        $where['id'] = array('in', rtrim($id, ','));
        $res = \think\Loader::model($model)->where($where)->delete();
        if ($res) {
            $this->success('删除成功', 'admin/PhoneType/index');
        } else {
            $this->error('删除失败');
        }
    }

}
