<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yo;

class ApiMapController extends Controller
{
  public function public()
  {
    $base_url = env('APP_URL');
    return [
      'Yo' => $base_url . '/api/yo'
    ];
  }

  public function mp()
  {
    $base_url = env('APP_URL');
    return [
      'ExampleMpPay' => $base_url . '/api/Example/Mp/pay'
    ];
  }

  public function admin()
  {
    $base_url = env('APP_URL');
    return [
      'AdminIpPoolCreate' => $base_url . '/api/Admin/IpPool/create',
      'AdminIpPoolUpdate' => $base_url . '/api/Admin/IpPool/update',
      'AdminIpPoolDelete' => $base_url . '/api/Admin/IpPool/delete',
      'AdminIpPoolList' => $base_url . '/api/Admin/IpPool/list',
      'WanLiuUpload' => $base_url . '/api/WanLiu/upload',
      'AdminWanLiuToken' => $base_url . '/api/Admin/WanLiu/token',
      'AdminAdminResetPassword' => $base_url . '/api/Admin/Admin/reset_password',
      'AdminConfigCreate' => $base_url . '/api/Admin/Config/create',
      'AdminConfigUpdate' => $base_url . '/api/Admin/Config/update',
      'AdminConfigDelete' => $base_url . '/api/Admin/Config/delete',
      'AdminConfigList' => $base_url . '/api/Admin/Config/list',
      'AdminAdminAuthSelect' => $base_url . '/api/Admin/AdminAuth/select',
      'AdminAdminAuthCreate' => $base_url . '/api/Admin/AdminAuth/create',
      'AdminAdminAuthUpdate' => $base_url . '/api/Admin/AdminAuth/update',
      'AdminAdminAuthDelete' => $base_url . '/api/Admin/AdminAuth/delete',
      'AdminAdminAuthList' => $base_url . '/api/Admin/AdminAuth/list',
      'AdminRequestLogTxt' => $base_url . '/api/Admin/RequestLog/txt',
      'AdminRequestLogList' => $base_url . '/api/Admin/RequestLog/list',
      'AdminUploadList' => $base_url . '/api/Admin/Upload/list',
      'AdminUploadDelete' => $base_url . '/api/Admin/Upload/delete',
      'AdminUploadSearch' => $base_url . '/api/Admin/Upload/search',
      'AdminAdminCreate' => $base_url . '/api/Admin/Admin/create',
      'AdminAdminUpdate' => $base_url . '/api/Admin/Admin/update',
      'AdminAdminDelete' => $base_url . '/api/Admin/Admin/delete',
      'AdminAdminList' => $base_url . '/api/Admin/Admin/list',
      'AdminAdminAuthGroupSelect' => $base_url . '/api/Admin/AdminAuthGroup/select',
      'AdminAdminAuthGroupCreate' => $base_url . '/api/Admin/AdminAuthGroup/create',
      'AdminAdminAuthGroupUpdate' => $base_url . '/api/Admin/AdminAuthGroup/update',
      'AdminAdminAuthGroupDelete' => $base_url . '/api/Admin/AdminAuthGroup/delete',
      'AdminAdminAuthGroupList' => $base_url . '/api/Admin/AdminAuthGroup/list',
      'AdminAdminAuthChoose' => $base_url . '/api/Admin/AdminAuth/choose',
      'AdminUploadImage' => $base_url . '/api/Admin/Upload/image',
      'AdminAdminUpdateSelf' => $base_url . '/api/Admin/Admin/update_self',
      'AdminAdminAccountChangePassword' => $base_url . '/api/Admin/AdminAccount/change_password',
      'AdminAdminAuthMenu' => $base_url . '/api/Admin/AdminAuth/menu',
      'AdminAdminQuit' => $base_url . '/api/Admin/Admin/quit',
      'AdminAdminStatus' => $base_url . '/api/Admin/Admin/status',
      'AdminAdminInfo' => $base_url . '/api/Admin/Admin/info',
      'AdminAdminLogin' => $base_url . '/api/Admin/Admin/login',
      'AdminImageCaptchaCreate' => $base_url . '/api/Admin/ImageCaptcha/create',
      'AdminConfigGet' => $base_url . '/api/Admin/Config/get',
    ];
  }

  public function get(Request $request)
  {
    $client = $request->get('client');
    $list = self::public();
    switch ($client) {
      case 'admin':
        $list = array_merge($list, self::admin());
        break;
      case 'mp':
        $list = array_merge($list, self::mp());
        break;
    }
    return Yo::echo([
      'list' => $list
    ]);
  }
}
