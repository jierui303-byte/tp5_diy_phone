<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//return [
//    '__pattern__' => [
//        'name' => '\w+',
//    ],
//    '[hello]'     => [
//        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//        ':name' => ['index/hello', ['method' => 'post']],
//    ],
//    'helloIndex/:id' => 'index/index/index',
//];

use think\Route;

Route::get('', 'index/index/index');
Route::get('he', 'index/index/he');
Route::get('canvas', 'index/index/canvas');
Route::get('show/:typeId/:varId', 'index/index/show');
Route::any('design/:typeId/:varId', 'index/index/design');
Route::any('ajaxGetPhoneTypes/:id', 'index/index/ajaxGetPhoneTypes');
Route::any('ajaxGetPhoneVarieties/:id', 'index/index/ajaxGetPhoneVarieties');
Route::get('ajaxGetMaskCategorys', 'index/index/ajaxGetMaskCategorys');//获取蒙版分类
Route::get('ajaxGetChartCategorys', 'index/index/ajaxGetChartCategorys');//获取贴图分类
Route::get('ajaxGetMaskCategoryPicturesById/:id', 'index/index/ajaxGetMaskCategoryPicturesById');//获取蒙版分类下的图片
Route::get('ajaxGetChartCategoryPicturesById/:id', 'index/index/ajaxGetChartCategoryPicturesById');//获取贴图分类下的图片
Route::post('ajaxPost', 'index/index/ajaxPost');//表单提交
Route::post('ajaxUploadImage', 'index/index/ajaxUploadImage');//高清图片上传提交


Route::get('admin', 'admin/index/index');
Route::any('adminLoginIndex', 'admin/login/index');

//管理员管理
Route::get('adminUsersIndex', 'admin/users/index');
Route::any('adminUsersAddUser', 'admin/users/addUser');
Route::any('adminUsersEditUser/:uid', 'admin/users/editUser');
Route::any('adminUsersDel', 'admin/users/del');
Route::any('adminUsersDelAll', 'admin/users/del_all');
Route::any('adminUsersAdminStop', 'admin/users/admin_stop');

//商铺管理
Route::get('adminShopsIndex', 'admin/shops/index');
Route::any('adminShopsAddUser', 'admin/shops/addUser');
Route::any('adminShopsEditUser/:uid', 'admin/shops/editUser');
Route::any('adminShopsDel', 'admin/shops/del');
Route::any('adminShopsDelAll', 'admin/shops/del_all');
Route::any('adminShopsAdminStop', 'admin/shops/admin_stop');

//角色管理
Route::get('adminRolesIndex', 'admin/roles/index');
Route::any('adminRolesAddRole', 'admin/roles/addRole');
Route::any('adminRolesEditRole/:id', 'admin/roles/editRole');
Route::any('adminRolesDel', 'admin/roles/del');
Route::any('adminRolesDelAll', 'admin/roles/del_all');

//权限管理
Route::get('adminPermissionsIndex', 'admin/permissions/index');
Route::any('adminPermissionsAddPermission', 'admin/permissions/addPermission');
Route::any('adminPermissionsEditPermission/:id', 'admin/permissions/editPermission');
Route::any('adminPermissionsDel', 'admin/permissions/del');
Route::any('adminPermissionsDelAll', 'admin/permissions/del_all');

//蒙版管理-分类列表
Route::get('adminMaskIndex', 'admin/mask/index');
Route::any('adminMaskAddCategory', 'admin/mask/addCategory');
Route::any('adminMaskEditCategory/:id', 'admin/mask/editCategory');
Route::any('adminMaskEditCategoryDel', 'admin/mask/del');
Route::any('adminMaskEditCategoryDelAll', 'admin/mask/del_all');

//蒙版管理-蒙版图片列表
Route::get('adminMaskShow/:id', 'admin/mask/show');
Route::any('adminMaskAddMaskPicture/:id', 'admin/mask/addMaskPicture');
Route::any('adminMaskEditMaskPicture/:id', 'admin/mask/editMaskPicture');
Route::any('adminMaskUploadMaskPicture', 'admin/mask/uploadMaskPicture');

//订单管理
Route::get('adminOrderIndex', 'admin/order/index');
Route::any('adminOrderAddOrder', 'admin/order/addOrder');
Route::any('adminOrderEditOrder/:id', 'admin/order/editOrder');
Route::any('adminOrderEditOrderDel', 'admin/order/del');
Route::any('adminOrderEditOrderDelAll', 'admin/order/del_all');

//手机机型-品牌列表
Route::get('adminPhoneTypeBrandIndex', 'admin/PhoneTypeBrand/index');
Route::any('adminPhoneTypeBrandAddPhoneTypeBrand', 'admin/PhoneTypeBrand/addPhoneTypeBrand');
Route::any('adminPhoneTypeBrandEditPhoneTypeBrand/:id', 'admin/PhoneTypeBrand/editPhoneTypeBrand');
Route::any('adminPhoneTypeBrandEditPhoneTypeBrandDel', 'admin/PhoneTypeBrand/del');
Route::any('adminPhoneTypeBrandEditPhoneTypeBrandDelAll', 'admin/PhoneTypeBrand/del_all');
Route::any('adminPhoneTypeBrandUploadPhoneTypeBrandPicture', 'admin/PhoneTypeBrand/uploadPhoneTypeBrandPicture');

//手机机型-机型列表
Route::get('adminPhoneTypeIndex/:brand_id', 'admin/PhoneType/index');
Route::any('adminPhoneTypeAddPhoneType/:brand_id', 'admin/PhoneType/addPhoneType');
Route::any('adminPhoneTypeEditPhoneType/:id', 'admin/PhoneType/editPhoneType');
Route::any('adminPhoneTypeEditPhoneTypeDel', 'admin/PhoneType/del');
Route::any('adminPhoneTypeEditPhoneTypeDelAll', 'admin/PhoneType/del_all');
Route::any('adminPhoneTypeUploadPhoneTypePicture', 'admin/PhoneType/uploadPhoneTypePicture');

//手机机型-品种管理
Route::get('adminPhoneVarietiesIndex/:type_id', 'admin/PhoneVarieties/index');
Route::any('adminPhoneVarietiesAddPhoneVarieties/:type_id', 'admin/PhoneVarieties/addPhoneVarieties');
Route::any('adminPhoneVarietiesEditPhoneVarieties/:id', 'admin/PhoneVarieties/editPhoneVarieties');
Route::any('adminPhoneVarietiesEditPhoneVarietiesDel', 'admin/PhoneVarieties/del');
Route::any('adminPhoneVarietiesEditPhoneVarietiesDelAll', 'admin/PhoneVarieties/del_all');
Route::any('adminPhoneVarietiesUploadPhoneVarietiesPicture', 'admin/PhoneVarieties/uploadPhoneVarietiesPicture');

//贴图管理-分类列表
Route::get('adminChartIndex', 'admin/chart/index');
Route::any('adminChartAddCategory', 'admin/chart/addCategory');
Route::any('adminChartEditCategory/:id', 'admin/chart/editCategory');
Route::any('adminChartEditCategoryDel', 'admin/chart/del');
Route::any('adminChartEditCategoryDelAll', 'admin/chart/del_all');

//贴图管理-蒙版图片列表
Route::get('adminChartShow/:id', 'admin/chart/show');
Route::any('adminChartAddMaskPicture/:id', 'admin/chart/addChartPicture');
Route::any('adminChartEditMaskPicture/:id', 'admin/chart/editChartPicture');
Route::any('adminChartUploadMaskPicture', 'admin/chart/uploadChartPicture');
