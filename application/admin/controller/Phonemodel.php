<?php

namespace app\admin\controller;

class Phonemodel extends Base
{
    public function index()
    {
        $model = new \app\admin\model\PhoneType();
        $data = $model::all();
        $this->assign('data', $data);
        return view("phonemodel/index");
    }

    public function add()
    {
        if ($this->request->isAjax()) {
            $model = new \app\admin\model\PhoneType();
            $model->Brand_name = request()->post('phonetype_name');
            $model->thumb_img = request()->post('imgUrl');
            $res = $model->save();
            if ($res) {
                $this->success('添加成功', 'admin/Phonetype/index');//成功跳转
            } else {
                $this->error('添加失败');//失败跳转
            }
        } else {
            $token = $this->request->token('__token__', 'sha1');//生成token 自定义令牌生成规则，可以调用Request类的token方法
            $this->assign('token', $token);
            return $this->fetch('add');
        }

//        $token = $this->request->token('__token__', 'sha1');//生成token 自定义令牌生成规则，可以调用Request类的token方法
//
//        $this->assign('token', $token);
//        return $this->fetch('add');
    }

    public function uploadPhoneTypePicture(Request $request)
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = $request->file('file');//可能多张，可能单张
        $fileInfo = $file->getInfo();
        $oldFileName = $fileInfo['name'];//上传图片原名称
        $oldFileType = $fileInfo['type'];//上传图片的原类型
        $old_tmp_name = $fileInfo['tmp_name'];//临时存放路径
        $newLeavePath = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'phoneType';//定义新的存储路径 /uploads/mask 蒙版目录下
        if ($file) {
            //支持对上传文件的验证，包括文件大小、文件类型和后缀
            $info = $file->validate(['size' => 1567800, 'ext' => 'jpg,png,gif'])
                ->move($newLeavePath);//移动到框架应用根目录/uploads/mask 蒙版目录下
            if ($info) {
                // 成功上传后 获取上传信息
                // 输出 jpg
//                echo $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
//                echo $info->getSaveName();
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
//                echo $info->getFilename();
                $newPath = '/uploads' . DS . 'phoneType/' . $info->getSaveName();
                //上传成功后，把图片最新路径和分类id分别存储在数据库中
//                httpStatus(200, array(
//                    'newPaht'=>$newPath,
//                ));
                return json_encode(['savepath' => $newPath]);
            } else {
                // 上传失败获取错误信息 失败返回数据格式
                httpStatus(500, array(
                    'msg' => $file->getError()
                ));
            }

        }

    }
}