<?php
namespace app\index\controller;

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
    public function index()
    {
        $brandLists = (new PhoneTypeBrand())->getAllListsByWhere(
            [
                'status' => 1
            ],
            ['id,brand_logo']
        );

        $this->assign('brandLists', $brandLists);
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

    public function show($typeId, $varId)
    {
        //获取当前品种名称信息
        $current = (new PhoneVarieties())->getOneById($varId);
        //把模板进行数组转化
        $current['var_template'] = json_decode($current['var_template']);

        //获取当前订单的总数
        $num = (new Order())->count('id');
        $orderNum = date("d").'00'.($num + 1);

        $this->assign('orderNum', $orderNum);
        $this->assign('current', $current);
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
        $data['name'] = $this->request->post('name');
        $data['tel'] = $this->request->post('tel');
        $data['address'] = $this->request->post('address');
        $data['pic'] = $this->request->post('pic');
        $data['create_time'] = date('y-m-d h:i:s',time());
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
