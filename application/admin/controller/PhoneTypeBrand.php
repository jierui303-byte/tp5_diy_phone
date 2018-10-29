<?php

namespace app\admin\controller;

use app\common\model\PhoneType;
use app\common\service\PhoneTypeBrand as PhoneTypeBrandService;
use think\Request;
use think\Validate;
use think\Db;

use think\Controller;

class PhoneTypeBrand extends Base
{
    public function index()
    {
        $arrS = (new PhoneTypeBrandService())->getAllListsByWhere(
            [
                'status' => 1
            ],
            ['id,brand_name,brand_logo,update_time']
        );
        $this->assign('data', $arrS);
        $this->assign('count', count($arrS));
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
        $newLeavePath = ROOT_PATH . 'uploads'.DS.'phoneTyleBrandPicture';//定义新的存储路径 /uploads/mask 蒙版目录下
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
                $newPath = $this->request->domain().'/uploads'.DS.'phoneTyleBrandPicture/'.$info->getSaveName();
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

    public function addPhoneTypeBrand()
    {
        if ($this->request->isAjax()){
            $data['brand_name'] = $this->request->post('brand_name');
            $data['brand_logo'] = $this->request->post('brand_logo');
            $data['create_time'] = date('y-m-d h:i:s',time());
            $res = (new PhoneTypeBrandService())->insert($data);//新增
            if($res){
                $this->success('新增成功', 'admin/PhoneTypeBrand/index');//成功跳转
            }else{
                $this->error('新增失败');//失败跳转
            }
        }else{
            $token = $this->request->token('__token__', 'sha1');//生成token 自定义令牌生成规则，可以调用Request类的token方法
            $this->assign('token', $token);
            return $this->fetch('add-phone-type-brand');
        }
    }

    public function editPhoneTypeBrand($id)
    {
        if ($this->request->isAjax()){
            $data['brand_name'] = $this->request->post('brand_name');
            $data['brand_logo'] = $this->request->post('brand_logo');
            $res = (new PhoneTypeBrandService())
                ->where('id', $id)
                ->update($data);//更新
            if($res){
                $this->success('修改成功', 'admin/PhoneTypeBrand/index');//成功跳转
            }else{
                $this->error('修改失败');//失败跳转
            }
        }else{
            //获取分类信息
            $res = (new PhoneTypeBrandService())->getOneById($id, ['id,brand_name,brand_logo']);
            $token = $this->request->token('__token__', 'sha1');//生成token 自定义令牌生成规则，可以调用Request类的token方法
            $this->assign('token', $token);
            $this->assign('data', $res);
            return $this->fetch('edit-phone-type-brand');
        }
    }

    //删除单个(公共方法)
    public function del()
    {
        $model = $this->request->post('model');//数据模型类名
        $id = $this->request->post('id');
        //删除之前先查询当前品牌下是否存在机型数据
        if((new PhoneType())->where('brand_id', $id)->count()){
            $this->error('该品牌下存在机型数据，不允许删除');
        }
        $res = \think\Loader::model($model)->where(['id'=>$id])->delete();
        if ($res) {
            $this->success('删除成功', 'admin/PhoneTypeBrand/index');
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
            $this->success('删除成功', 'admin/PhoneTypeBrand/index');
        } else {
            $this->error('删除失败');
        }
    }

}
