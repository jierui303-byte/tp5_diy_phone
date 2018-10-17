<?php

namespace app\admin\controller;

use app\common\service\PhoneVarieties as PhoneVarietiesService;
use think\Request;
use think\Validate;
use think\Db;

use think\Controller;

class PhoneVarieties extends Base
{
    public function index($type_id)
    {
        $arrS = (new PhoneVarietiesService())->getAllListsByWhere(
            [
                'status' => 1,
                'type_id' => $type_id
            ],
            ['id,var_name,var_type,var_template,update_time']
        );
        foreach($arrS as $k=>$v){
            $arrS[$k]['var_template'] = json_decode($v['var_template'], true);
        }
        $this->assign('data', $arrS);
        $this->assign('count', count($arrS));
        $this->assign('type_id', $type_id);
        return $this->fetch('index');
    }

    public function uploadPhoneTypeBrandPicture()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');//可能多张，可能单张
        $fileInfo = $file->getInfo();
        $oldFileName = $fileInfo['name'];//上传图片原名称
        $oldFileType = $fileInfo['type'];//上传图片的原类型
        $old_tmp_name = $fileInfo['tmp_name'];//临时存放路径
        $newLeavePath = ROOT_PATH . 'uploads'.DS.'phoneVarieties';//定义新的存储路径 /uploads/mask 蒙版目录下
        if($file){
            //支持对上传文件的验证，包括文件大小、文件类型和后缀
            $info = $file->validate(['size'=>1567800,'ext'=>'jpg,png,gif'])
                ->move($newLeavePath);//移动到框架应用根目录/uploads/mask 蒙版目录下
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
//                echo $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
//                echo $info->getSaveName();
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
//                echo $info->getFilename();
                $newPath = $this->request->domain().'/uploads'.DS.'phoneVarieties/'.$info->getSaveName();
                //上传成功后，把图片最新路径和分类id分别存储在数据库中
                return json_encode(['savepath' => $newPath]);
            }else{
                // 上传失败获取错误信息 失败返回数据格式
                httpStatus(500, array(
                    'msg'=>$file->getError()
                ));
            }

        }

    }

    public function addPhoneVarieties($type_id)
    {
        if ($this->request->isAjax()){
            $data['var_name'] = $this->request->post('var_name');
            $data['var_template'] = json_encode(array(
                $this->request->post('var_template1'),
                $this->request->post('var_template2')
            ));
            $data['var_type'] = $this->request->post('var_type');
            $data['type_id'] = $this->request->post('type_id');
            $data['create_time'] = date('y-m-d h:i:s',time());
            $res = (new PhoneVarietiesService())->insert($data);//新增
            if($res){
                $this->success('新增成功', 'admin/PhoneVarieties/index');//成功跳转
            }else{
                $this->error('新增失败');//失败跳转
            }
        }else{
            $token = $this->request->token('__token__', 'sha1');//生成token 自定义令牌生成规则，可以调用Request类的token方法
            $this->assign('token', $token);
            $this->assign('type_id', $type_id);
            return $this->fetch('add-phone-varieties');
        }
    }

    public function editPhoneVarieties($id)
    {
        if ($this->request->isAjax()){
            $data['var_name'] = $this->request->post('var_name');
            $data['var_template'] = json_encode(array(
                $this->request->post('var_template1'),
                $this->request->post('var_template2')
            ));
            $data['var_type'] = $this->request->post('var_type');
            $data['type_id'] = $this->request->post('type_id');
            $res = (new PhoneVarietiesService())
                ->where('id', $id)
                ->update($data);//更新
            if($res){
                $this->success('修改成功', 'admin/PhoneVarieties/index');//成功跳转
            }else{
                $this->error('修改失败');//失败跳转
            }
        }else{
            //获取分类信息
            $res = (new PhoneVarietiesService())->getOneById($id, ['id,var_name,var_type,var_template,type_id']);
            $res['var_template'] = json_decode($res['var_template'], true);
            $token = $this->request->token('__token__', 'sha1');//生成token 自定义令牌生成规则，可以调用Request类的token方法
            $this->assign('token', $token);
            $this->assign('data', $res);
            return $this->fetch('edit-phone-varieties');
        }
    }

    //删除单个(公共方法)
    public function del()
    {
        $model = $this->request->post('model');//数据模型类名
        $id = $this->request->post('id');
        $res = \think\Loader::model($model)->where(['id'=>$id])->delete();
        if ($res) {
            $this->success('删除成功', 'admin/PhoneVarieties/index');
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
            $this->success('删除成功', 'admin/PhoneVarieties/index');
        } else {
            $this->error('删除失败');
        }
    }

}
