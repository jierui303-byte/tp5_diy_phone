<?php
namespace app\admin\controller;

use think\Controller;
use app\common\service\Order as OrderService;//引入数据库增删改查方法api类
use app\common\service\MaskPicture;
use think\Db;
use think\Validate;
use think\Session;

class Order extends Base
{
    public function index()
    {
        //获取当前登录用户id
        $uid = Session::get('uid');
//        var_dump($uid);
//        exit;
        $arrS = (new OrderService())->getAllListsByWhere(
            [
                'status' => 1,
                'uid' => $uid
            ],
            ['id,name,tel,address,pic,order_num,phone_type_id,phone_type_name,phone_varieties_name,phone_varieties_id,var_type_name,var_type_id,create_time']
        );

        //统计当日数据条数
        $td = date("Y-m-d");
        $tm = date("Y-m-d", strtotime("+1 day"));
//        var_dump($td, $tm);
        $arrSToday = (new \app\common\model\Order())
            ->whereTime('create_time','between',[$td, $tm])
            ->where('uid', $uid)
            ->where('status', 1)
            ->select();

        $this->assign('data', $arrS);
        $this->assign('count', count($arrS));
        $this->assign('todyCount', count($arrSToday));
        return $this->fetch('index');
    }

    public function show($id)
    {
        $routePathInfo = $this->request->path();
        $routeName = explode('/', $routePathInfo)[0];
        $arrS = (new OrderService())->getAllListsByWhere(
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
        $arrS = (new OrderService())->getAllListsByWhere(
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
            $res = (new OrderService())->insert($data);//新增
            if($res){
                $this->success('新增成功', 'admin/order/index');//成功跳转
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
            $res = (new OrderService())
                ->where('id', $data['id'])
                ->update($data);//更新
            if($res){
                $this->success('修改成功', 'admin/order/index');//成功跳转
            }else{
                $this->error('修改失败');//失败跳转
            }
        }else{
            //获取分类信息
            $res = (new OrderService())->getOneById($id, ['id,cate_name']);
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
        $res = \think\Loader::model($model)->where(['id'=>$id])->update(array(
            'status'=>0
        ));
        if ($res) {
            //订单做假删除 订单图片等7天后自动删除脚本进行删除
           $this->success('删除成功', 'admin/order/index');
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
            $this->success('删除成功', 'admin/order/index');
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

    //文件下载单个图片下载
    function downLoadImg($imgUrl){
        //获取到图片的名称
        $urls = pathinfo(urldecode($imgUrl));
        $file_url = ROOT_PATH.'public/'.parse_url($urls['dirname'])['path'].'/'.$urls['basename'];
//        var_dump( ROOT_PATH );
//        exit;

        $new_name='';
        if(!isset($file_url) || trim($file_url) == ''){
            echo '500';
        }
        if(!file_exists($file_url)){ //检查文件是否存在
            echo '404';
        }
        $file_name = basename($file_url);
        $file_type = explode('.', $file_url);
        $file_type = $file_type[count($file_type) - 1];
        $file_name = trim($new_name=='') ? $file_name : urlencode($new_name);
        $file_type = fopen($file_url, 'r'); //打开文件
        //输入文件标签
        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length: ".filesize($file_url));
        header("Content-Disposition: attachment; filename=".$file_name);
        //输出文件内容
        echo fread($file_type, filesize($file_url));
        fclose($file_type);
    }

    //批量图片打包成Zip下载
    public function downLoadImgAll()
    {

    }

}



