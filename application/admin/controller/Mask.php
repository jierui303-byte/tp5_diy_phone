<?php
namespace app\admin\controller;

use app\common\model\MaskCategory;
use think\Controller;
use app\common\service\MaskCategory as MaskCategoryService;//引入数据库增删改查方法api类
use app\common\service\MaskPicture;
use think\Db;
use think\Validate;

class Mask extends Base
{
    public function _empty()
    {
        return $this->index();
    }

    public function index()
    {
        $arrS = (new MaskCategoryService())->getAllListsByWhere(
            [
                'status' => 1
            ],
            ['id,cate_name,update_time']
        );
        $this->assign('data', $arrS);
        $this->assign('count', count($arrS));
        return $this->fetch('index');
    }

    public function show($id)
    {
        $routePathInfo = $this->request->path();
        $routeName = explode('/', $routePathInfo)[0];
        $arrS = (new MaskCategoryService())->getAllListsByWhere(
            ['status' => 1],
            ['id,cate_name,update_time']
        );//获取分类列表
        $picList = (new MaskPicture())->getAllListsByWhere(
            ['cate_id'=>$id],
            ['id,cate_id,pic_old_name,pic_path']
        );//获取相应分类的图片
        $this->assign('data', $arrS);
        $this->assign('count', count($picList));
        $this->assign('picList', $picList);
        $this->assign('currentId', $id);
        $this->assign('ROOT_PATH', $this->request->domain());
        $this->assign('routeName', $routeName);
        $this->assign('ext', $this->request->ext());
        return $this->fetch('show');
    }

    public function uploadMaskPicture()
    {
        $maskCategoryId = $this->request->post('maskCategoryId');
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');//可能多张，可能单张
        $fileInfo = $file->getInfo();
        $oldFileName = $fileInfo['name'];//上传图片原名称
        $oldFileType = $fileInfo['type'];//上传图片的原类型
        $old_tmp_name = $fileInfo['tmp_name'];//临时存放路径
        $newLeavePath = ROOT_PATH . 'uploads'.DS.'mask';//定义新的存储路径 /uploads/mask 蒙版目录下
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
                $newPath = $this->request->domain().'/uploads'.DS.'mask/'.$info->getSaveName();
                //上传成功后，把图片最新路径和分类id分别存储在数据库中
                $res = (new MaskPicture())->insert(array(
                    'cate_id' => $maskCategoryId,
                    'pic_old_name' => $oldFileName,
                    'pic_path' => $newPath,
                    'create_time' => date('y-m-d h:i:s',time())
                ));
                if($res){
                    httpStatus(200, array(
                        'newPaht'=>$newPath,
                        'maskCategoryId'=>$maskCategoryId
                    ));
                }else{
                    httpStatus(500, array(
                        'msg'=>'写入数据库失败'
                    ));
                }
            }else{
                // 上传失败获取错误信息 失败返回数据格式
                httpStatus(500, array(
                    'msg'=>$file->getError()
                ));
            }

        }

    }

    public function addMaskPicture($id)
    {
        $cate_id = $id;//分类id
        $arrS = (new MaskCategoryService())->getAllListsByWhere(
            ['status' => 1],
            ['id,cate_name,update_time']
        );
        $this->assign('data', $arrS);
        $this->assign('cate_id', $cate_id);
        return $this->fetch('add-mask-picture');
    }

    public function editMaskPicture($id)
    {
        return $this->fetch('edit-mask-picture');
    }

    public function addCategory()
    {
        if ($this->request->isAjax()){
            $data['cate_name'] = $this->request->post('cate_name');
            $data['create_time'] = date('y-m-d h:i:s',time());
            $res = (new MaskCategoryService())->insert($data);//新增
            if($res){
                $this->success('新增成功', 'admin/mask/index');//成功跳转
            }else{
                $this->error('新增失败');//失败跳转
            }
        }else{
            $token = $this->request->token('__token__', 'sha1');//生成token 自定义令牌生成规则，可以调用Request类的token方法
            $this->assign('token', $token);
            return $this->fetch('add-category');
        }
    }

    public function editCategory($id)
    {
        if ($this->request->isAjax()){
            $data['cate_name'] = $this->request->post('cate_name');
            $data['id'] = $this->request->post('id');
            $res = (new MaskCategoryService())
                ->where('id', $data['id'])
                ->update($data);//更新
            if($res){
                $this->success('修改成功', 'admin/mask/index');//成功跳转
            }else{
                $this->error('修改失败');//失败跳转
            }
        }else{
            //获取分类信息
            $res = (new MaskCategoryService())->getOneById($id, ['id,cate_name']);
            $token = $this->request->token('__token__', 'sha1');//生成token 自定义令牌生成规则，可以调用Request类的token方法
            $this->assign('token', $token);
            $this->assign('data', $res);
            return $this->fetch('edit-category');
        }
    }

    //删除单个(公共方法)
    public function del()
    {
        $model = $this->request->post('model');//数据模型类名
        $id = $this->request->post('id');
        if($model == 'MaskCategory'){
            //分类删除 检测分类下是否有图片
            if((new \app\common\model\MaskPicture())->where('cate_id', $id)->count()){
                $this->error('该分类下存在图片数据，不允许删除');
            }
        }
        $res = \think\Loader::model($model)->where(['id'=>$id])->delete();
        if ($res) {
           $this->success('删除成功', 'admin/mask/index');
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
            $this->success('删除成功', 'admin/mask/index');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 返回状态码
     * @param $code     状态码
     * @param $message  信息
     */
    public function ajaxRtn($code, $status, $message){
        $msg = ['code'=>$code,'status'=>$status,'errorMsg'=>$message];
        echo  json_encode($msg);
    }

}
