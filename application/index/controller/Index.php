<?php
namespace app\index\controller;

use app\common\model\AuthGroupAccess;
use app\common\model\Users;
use app\common\service\ChartCategory;
use app\common\service\ChartPicture;
use app\common\service\MaskCategory;
use app\common\service\MaskPicture;
use app\common\service\Order;
use app\common\service\PhoneType;
use app\common\service\PhoneTypeBrand;
use app\common\service\PhoneVarieties;
use think\Request;
use think\Validate;

use think\Controller;

class Index extends Base
{
    public function index($userId)
    {
        $userInfo = (new Users())->find($userId);
        //此处可以判断是否是经过二维码扫描进入本页面的

        //还需要判断当前用户的身份是否是商户，是商户才能访问  默认2为商户
        $authGroupAccess = (new AuthGroupAccess())->where('uid', $userId)->find();
        if($authGroupAccess){
            //判断是商户还是普通用户
            if($authGroupAccess['group_id'] == 2){
                //另外需要判断当前用户的状态是否正常  已经到期的不能再访问
                $status = $userInfo['status'];
                if($status == 1){
                    //判断是否在营运的有效期内
                    //判断商户的有效期是否到期，是否在有效期范围内，不再范围内不允许访问
                    $start_time = $userInfo['start_time'];
                    $end_time = $userInfo['end_time'];
                    $currentTime = time();

                }else{
                    //为商户,判断商户状态为0时，也就是认证状态被关闭  只有认证过的才能允许访问
                    return array(
                        'code' => 0,
                        'msg' => '该账户状态不正常，请联系网站管理员!'
                    );
                }
            }else{
                return array(
                    'code' => 0,
                    'msg' => '该账户不具备访问店铺资格，请联系网站管理员!'
                );
            }
        }else{
            return array(
                'code' => 0,
                'msg' => '该账户状态不正常，请联系网站管理员!'
            );
        }


        $brandLists = (new PhoneTypeBrand())->getAllListsByWhere(
            [
                'status' => 1
            ],
            ['id,brand_logo']
        );

        $this->assign('brandLists', $brandLists);
        $this->assign('userId', $userId);//商家id
        return $this->fetch('index');
    }

    public function canvas()
    {
        return $this->fetch('canvas');
    }

    public function design($typeId, $varId)
    {
        //获取当前品种名称信息
        $current = (new PhoneVarieties())->getOneById($varId);

        $this->assign('current', $current);
        return $this->fetch('design');
    }

    public function show($userId, $typeId, $varId)
    {
        //获取当前品种名称信息
        $current = (new PhoneVarieties())->getOneById($varId);
        //把模板进行数组转化
        $current['var_template'] = json_decode($current['var_template']);

        //获取当前订单的总数
        $num = (new Order())->count('id');
        $orderNum = date("d").'00'.($num + 1);

        //获取当前的手机机型 品种名称 品种类型
        $currentPhoneType = (new PhoneType())->getOneById($typeId);
        $phoneTypeName = $currentPhoneType['type_name'];//手机机型
        $phoneTypeNameId = $currentPhoneType['id'];//手机机型
        $phoneVarietiesName = $current['var_name'];//品种名称
        $phoneVarietiesNameId = $current['id'];//品种名称
        if($current['var_type'] == 1){
            $varTypeName = '手机壳';//品种类型
            $varTypeNameId = $current['var_type'];//品种类型
        }else{
            $varTypeName = '手机彩膜';//品种类型
            $varTypeNameId = $current['var_type'];//品种类型
        }


        $this->assign('orderNum', $orderNum);
        $this->assign('current', $current);
        $this->assign('phoneTypeName', $phoneTypeName);
        $this->assign('phoneTypeNameId', $phoneTypeNameId);
        $this->assign('phoneVarietiesName', $phoneVarietiesName);
        $this->assign('phoneVarietiesNameId', $phoneVarietiesNameId);
        $this->assign('varTypeName', $varTypeName);
        $this->assign('varTypeNameId', $varTypeNameId);
        $this->assign('userId', $userId);
        return $this->fetch('show');
    }

    public function he()
    {
        return $this->fetch('he');
    }


    //获取机型列表
    public function ajaxGetPhoneTypes($id)
    {
        $typeLists = (new PhoneType())->getAllListsByWhere(
            ['status'=>1,'brand_id'=>$id],
            ['id,type_name,brand_id']
        );

       return array(
           'code'=>1,
           'msg'=>'ok',
           'data'=>$typeLists
       );
    }

    //获取机型列表
    public function ajaxGetPhoneVarieties($id)
    {
        $typeLists = (new PhoneVarieties())->getAllListsByWhere(
            ['status'=>1,'type_id'=>$id],
            ['id,var_name,type_id']
        );

        return array(
            'code'=>1,
            'msg'=>'ok',
            'data'=>$typeLists
        );
    }

    //获取蒙版分类
    public function ajaxGetMaskCategorys()
    {
        $typeLists = (new MaskCategory())->getAllListsByWhere(
            ['status'=>1],
            ['id,cate_name']
        );

        return array(
            'code'=>1,
            'msg'=>'ok',
            'data'=>$typeLists
        );
    }

    //获取蒙版分类
    public function ajaxGetMaskCategoryPicturesById($id)
    {
        $typeLists = (new MaskPicture())->getAllListsByWhere(
            ['cate_id'=>$id],
            ['id,pic_path,cate_id']
        );

        return array(
            'code'=>1,
            'msg'=>'ok',
            'data'=>$typeLists
        );
    }

    //获取贴图分类
    public function ajaxGetChartCategorys()
    {
        $typeLists = (new ChartCategory())->getAllListsByWhere(
            ['status'=>1],
            ['id,cate_name']
        );

        return array(
            'code'=>1,
            'msg'=>'ok',
            'data'=>$typeLists
        );
    }

    //获取贴图分类下图片
    public function ajaxGetChartCategoryPicturesById($id)
    {
        $typeLists = (new ChartPicture())->getAllListsByWhere(
            ['cate_id'=>$id],
            ['id,pic_path,cate_id']
        );

        return array(
            'code'=>1,
            'msg'=>'ok',
            'data'=>$typeLists
        );
    }

    //表单提交
    public function ajaxPost()
    {
        $data['order_num'] = $this->request->post('orderNum');
        $data['phone_type_id'] = $this->request->post('PhoneTypeId');
        $data['phone_type_name'] = $this->request->post('PhoneType');
        $data['phone_varieties_id'] = $this->request->post('PhoneVarietiesId');
        $data['phone_varieties_name'] = $this->request->post('PhoneVarieties');
        $data['var_type_id'] = $this->request->post('var_type_id');
        $data['var_type_name'] = $this->request->post('var_type');
        $data['name'] = $this->request->post('name');
        $data['tel'] = $this->request->post('tel');
        $data['address'] = $this->request->post('address');
        $data['pic'] = $this->request->post('pic');
        $data['uid'] = $this->request->post('userId');//用户id
        $data['create_time'] = date('y-m-d h:i:s', time());
        if(!$data['name'] || !$data['tel'] || !$data['address'] || !$data['pic']){
            $this->error('表单信息不能为空');
        }
        $res = (new Order())->insert($data);
        if($res){
            $this->success('表单提交成功', 'index/index/design');//成功跳转
        }else{
            $this->error('表单提交失败');//失败跳转
        }
    }

    //canvas生成高清图片上传
    public function ajaxUploadImage()
    {
        $img_base64 = $this->request->post('imgBase64');

        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img_base64, $result)) {
            $type = $result[2];//文件类型
            $name = time() . '_' . mt_rand() .".{$type}";//自定义文件名称
            $new_file = "upload/active/img/".date('Ymd',time())."/";//文件夹位置
            if(!file_exists($new_file)){
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($new_file, 0777, true);//如果创建的多级目录,第三个参数设置为true
            }
            $new_file = $new_file.time().".{$type}";
            if(file_put_contents($new_file, base64_decode(str_replace($result[1], '', $img_base64)))){
                chmod($new_file, 0777);
                //修改图片的分辨率
                $AutoImage = new AutoImage();
                $new_file = $AutoImage->resize($new_file, 504, 1064);
//                echo $new_file;
                return array(
                    'code' => 1,
                    'msg' => '新文件保存成功',
                    'data' => 'http://'.$_SERVER['SERVER_NAME'].'/'.$new_file
                    );
            }else{
                return array(
                    'code' => 0,
                    'msg' => '新文件保存失败',
                    'data' => ''
                    );
            }

        }

    }

}

class AutoImage{
    private $image;

    public function resize($src, $width, $height){
        //$src 就是 $_FILES['upload_image_file']['tmp_name']
        //$width和$height是指定的分辨率
        //如果想按指定比例放缩，可以将$width和$height改为$src的指定比例
        $this->image = $src;
        $info = getimagesize($src);//获取图片的真实宽、高、类型
        if($info[0] == $width && $info[1] == $height){
            //如果分辨率一样，直接返回原图
            return $src;
        }
        switch ($info['mime']){
            case 'image/jpeg':
                header('Content-Type:image/jpeg');
                $image_wp = imagecreatetruecolor($width, $height);
                $image_src = imagecreatefromjpeg($src);
                imagecopyresampled($image_wp,  $image_src, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
                imagedestroy($image_src);
                imagejpeg($image_wp,$this->image);
                break;
            case 'image/png':
                header('Content-Type:image/png');
                $image_wp = imagecreatetruecolor($width, $height);
                $image_src = imagecreatefrompng($src);
                imagecopyresampled($image_wp, $image_src, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
                imagedestroy($image_src);
                imagejpeg($image_wp,$this->image);
                break;
            case 'image/gif':
                header('Content-Type:image/gif');
                $image_wp = imagecreatetruecolor($width, $height);
                $image_src = imagecreatefromgif($src);
                imagecopyresampled($image_wp, $image_src, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
                imagedestroy($image_src);
                imagejpeg($image_wp,$this->image);
                break;

        }

        return $this->image;

    }
}
