<?php

namespace App\Http\Controllers;

use App\Http\Request\ChangeAdminPassword;
use App\Models\Admin;
use App\Models\AdminAccount;
use Illuminate\Http\Request;
use Login;
use Yo;

class AdminAccountController extends Controller
{
  public function change_password(ChangeAdminPassword $request)
  {
    Login::admin();
    $hash = $request->post('hash');
    $code = $request->post('code');
    $time = $request->post('time');
    $uuid = $request->post('uuid');
    $captcha = new ImageCaptchaController();
    $captcha_check = $captcha->check($hash, $code, $time, $uuid);
    if ($captcha_check != 0) Yo::error_echo($captcha_check);
    $old_password = $request->post('old_password');
    $password = $request->post('password');
    $admin_account = AdminAccount::where('admin', Login::$info->id)
      ->where('type', 1)
      ->where('del', 2)
      ->first();
    if (!$admin_account) Yo::error_echo(100001, ['账号']);
    if (!password_verify($old_password, $admin_account->secret)) Yo::error_echo(100008);
    if ($old_password == $password) Yo::error_echo(100009);
    $admin_account->secret = bcrypt($password);
    $admin_account->save();
    if (Login::$info->initial_password == 1) {
      Login::$info->initial_password = 2;
      Login::$info->save();
    }
    return Yo::update_echo(Login::$info->init_password);
  }
}
