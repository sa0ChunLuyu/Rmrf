<?php

namespace App\Http\Controllers;

use App\Http\Request\EditAdmin;
use App\Http\Request\UpdateAdminInfo;
use App\Models\Admin;
use App\Models\AdminAccount;
use App\Models\AdminToken;
use App\Models\Config;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yo;
use Lu;
use Login;
use Illuminate\Support\Str;

class AdminController extends Controller
{
  public function reset_password(Request $request)
  {
    Login::admin(['admin-list']);
    $id = $request->post('id');
    $admin = Admin::where('id', $id)->where('del', 2)->first();
    if (!$admin) Yo::error_echo(100001, ['管理员']);
    $admin_account = AdminAccount::where('admin', $admin->id)->where('del', 2)->first();
    if (!$admin_account) Yo::error_echo(100001, ['管理员']);
    $password = Str::password(16);
    $admin->initial_password = 1;
    $admin->save();
    $admin_account->secret = bcrypt($password);
    $admin_account->save();
    return Yo::echo([
      'password' => $password
    ]);
  }

  public function create(EditAdmin $request)
  {
    Login::admin(['admin-list']);
    $account = $request->post('account');
    $admin_account = AdminAccount::where('account', $account)->where('type', 1)->where('del', 2)->first();
    if ($admin_account) Yo::error_echo(100023);
    $admin = new Admin();
    $admin->nickname = $request->post('nickname');
    $admin->avatar = $request->post('avatar') ?? '';
    $admin->admin_auth_group = $request->post('admin_auth_group');
    $admin->initial_password = $request->post('initial_password');
    $admin->status = $request->post('status');
    $admin->save();
    $admin_account = new AdminAccount();
    $admin_account->admin = $admin->id;
    $admin_account->account = $account;
    $admin_account->secret = bcrypt($request->post('password'));
    $admin_account->type = 1;
    $admin_account->save();
    return Yo::create_echo($admin->id);
  }

  public function update(EditAdmin $request)
  {
    Login::admin(['admin-list']);
    $id = $request->post('id');
    $account = $request->post('account');
    $admin_account = AdminAccount::where('admin', '!=', $id)->where('account', $account)->where('type', 1)->where('del', 2)->first();
    if ($admin_account) Yo::error_echo(100023);
    $admin = Admin::where('id', $id)->where('del', 2)->first();
    if (!$admin) Yo::error_echo(100001, ['管理员']);
    $admin_account = AdminAccount::where('admin', $id)->where('del', 2)->first();
    if (!$admin_account) Yo::error_echo(100001, ['管理员']);
    $admin->nickname = $request->post('nickname');
    $admin->avatar = $request->post('avatar') ?? '';
    $admin->admin_auth_group = $request->post('admin_auth_group');
    $admin->initial_password = $request->post('initial_password');
    $admin->status = $request->post('status');
    $admin->save();
    if ($admin_account->account != $account) {
      $admin_account->account = $request->post('account');
      $admin_account->save();
    }
    return Yo::update_echo($admin->id);
  }

  public function delete(Request $request)
  {
    Login::admin(['admin-list']);
    $id = $request->post('id');
    $admin = Admin::where('id', $id)->where('del', 2)->first();
    if (!$admin) Yo::error_echo(100001, ['管理员']);
    $admin_account = AdminAccount::where('admin', $id)->where('del', 2)->first();
    if (!$admin_account) Yo::error_echo(100001, ['管理员']);
    $admin->del = 1;
    $admin->save();
    $admin_account->del = 1;
    $admin_account->save();
    return Yo::delete_echo($admin->id);
  }

  public function list(Request $request)
  {
    Login::admin(['admin-list']);
    $status = $request->post('status');
    $search = $request->post('search');
    $admin_auth_group = $request->post('admin_auth_group');
    $initial_password = $request->post('initial_password');
    $admin_list = Admin::select([
      DB::raw('admins.id as id'),
      DB::raw('admins.nickname as nickname'),
      DB::raw('admins.avatar as avatar'),
      DB::raw('admins.status as status'),
      DB::raw('admins.admin_auth_group as admin_auth_group'),
      DB::raw('admins.initial_password as initial_password'),
      DB::raw('admin_accounts.account as account'),
      DB::raw("IFNULL(admin_auth_groups.name,'') as admin_auth_group_name")
    ])
      ->leftJoin('admin_accounts', function (JoinClause $join) {
        $join->on('admin_accounts.admin', '=', 'admins.id')
          ->where('admin_accounts.type', '=', 1);
      })
      ->leftJoin('admin_auth_groups', 'admin_auth_groups.id', '=', 'admins.admin_auth_group')
      ->where(function ($query) use ($status) {
        if ($status != 0) $query->where('admins.status', $status);
      })
      ->where(function ($query) use ($admin_auth_group) {
        if ($admin_auth_group != 0) $query->where('admins.admin_auth_group', $admin_auth_group);
      })
      ->where(function ($query) use ($initial_password) {
        if ($initial_password != 0) $query->where('admins.initial_password', $initial_password);
      })
      ->where(function ($query) use ($search) {
        if ($search != '') $query->where('admins.nickname', 'like', "%$search%");
      })
      ->where('admins.del', 2)
      ->paginate(20);
    return Yo::echo([
      'list' => $admin_list
    ]);
  }

  public function quit()
  {
    Login::admin_check();
    if (!!Login::$token) {
      Login::$token->del = 1;
      Login::$token->save();
    }
    return Yo::echo();
  }

  public function update_self(UpdateAdminInfo $request)
  {
    Login::admin();
    $nickname = $request->post('nickname');
    $avatar = $request->post('avatar');
    Login::$info->nickname = $nickname;
    Login::$info->avatar = $avatar;
    Login::$info->save();
    return Yo::update_echo(Login::$info->id);
  }

  public function login(Request $request)
  {
    $captcha_type_config = Config::where('name', '后台密码登录验证')->first();
    if (!!$captcha_type_config) {
      if ($captcha_type_config->value != '0') {
        $hash = $request->post('hash');
        $code = $request->post('code');
        $time = $request->post('time');
        $uuid = $request->post('uuid');
        $captcha = null;
        switch ($captcha_type_config->value) {
          case '1':
            $captcha = new ImageCaptchaController();
            break;
        }
        $captcha_check = $captcha->check($hash, $code, $time, $uuid);
        if ($captcha_check != 0) Yo::error_echo($captcha_check);
      }
    }
    $account = $request->post('account');
    $password = $request->post('password');
    $type = 1;
    $admin_account = AdminAccount::where('account', $account)
      ->where('type', $type)
      ->where('del', 2)
      ->first();
    if (!$admin_account) Yo::error_echo(100007);
    if (!password_verify($password, $admin_account->secret)) Yo::error_echo(100007);
    $admin = Admin::where('id', $admin_account->admin)
      ->where('status', 1)
      ->where('del', 2)
      ->first();
    if (!$admin) Yo::error_echo(100003);
    Login::$info = $admin;
    Login::$type = 'admin';
    $token = $this->create_token($admin, $type);
    return Yo::echo([
      'token' => $token
    ]);
  }

  public function status()
  {
    Login::admin();
    return Yo::echo();
  }

  public function info()
  {
    Login::admin();
    $token_ip_info = [
      'ip' => Login::$token->ip,
      'region' => Login::$token->region,
      'created_at' => date("Y-m-d H:i:s", strtotime(Login::$token->created_at)),
    ];
    $last_time_token = AdminToken::where('admin', Login::$info->id)
      ->where('id', '!=', Login::$token->id)
      ->orderBy('id', 'desc')->first();
    if ($last_time_token) {
      $last_time_token_ip_info = [
        'ip' => $last_time_token->ip,
        'region' => $last_time_token->region,
        'created_at' => date("Y-m-d H:i:s", strtotime($last_time_token->created_at)),
      ];
    } else {
      $last_time_token_ip_info = false;
    }
    return Yo::echo([
      'info' => [
        'id' => Login::$info->id,
        'nickname' => Login::$info->nickname,
        'avatar' => Login::$info->avatar,
        'initial_password' => Login::$info->initial_password,
        'token_ip_info' => $token_ip_info,
        'last_time_token_ip_info' => $last_time_token_ip_info,
      ]
    ]);
  }

  public function create_token($info, $type = 1): string
  {
    if ($info->status != 1) Yo::error_echo(100003);
    if ($info->del != 2) Yo::error_echo(100003);
    $token_str = Str::orderedUuid();
    $token = new AdminToken();
    $token->admin = $info->id;
    $token->token = $token_str;
    $ip = Lu::ip();
    $token->ip = Lu::ip();
    $token->region = '';
    $region_save_config = Config::where('name', '后台IP地区信息')->first();
    if ($region_save_config->value == '1') {
      $ip2region = new \Ip2Region();
      $record = $ip2region->simple($ip);
      if ($record !== false) {
        $token->region = $record;
      }
    }
    // $type 1-密码登录
    $token->type = $type;
    $token->save();
    return $token_str;
  }
}
