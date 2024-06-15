<?php

namespace App\Http\Controllers;

use App\Http\Request\EditAdminAuthGroup;
use App\Models\AdminAuthGroup;
use Illuminate\Http\Request;
use Login;
use Yo;

class AdminAuthGroupController extends Controller
{
  public function create(EditAdminAuthGroup $request)
  {
    Login::admin(['admin-auth']);
    $admin_auth_group = new AdminAuthGroup();
    $admin_auth_group->name = $request->post('name');
    $admin_auth_group->admin_auths = $request->post('admin_auths');
    $admin_auth_group->remark = $request->post('remark') ?? '';
    $admin_auth_group->status = $request->post('status');
    $admin_auth_group->save();
    return Yo::create_echo($admin_auth_group->id);
  }

  public function update(EditAdminAuthGroup $request)
  {
    Login::admin(['admin-auth']);
    $admin_auth_group = AdminAuthGroup::where('id', $request->post('id'))
      ->where('del', 2)->first();
    if (!$admin_auth_group) Yo::error_echo(100001, ['权限组']);
    $admin_auth_group->name = $request->post('name');
    $admin_auth_group->admin_auths = $request->post('admin_auths');
    $admin_auth_group->remark = $request->post('remark') ?? '';
    $admin_auth_group->status = $request->post('status');
    $admin_auth_group->save();
    return Yo::update_echo($admin_auth_group->id);
  }

  public function delete(Request $request)
  {
    Login::admin(['admin-auth']);
    $admin_auth_group = AdminAuthGroup::where('id', $request->post('id'))
      ->where('del', 2)->first();
    if (!$admin_auth_group) Yo::error_echo(100001, ['权限组']);
    $admin_auth_group->del = 1;
    $admin_auth_group->save();
    return Yo::delete_echo($admin_auth_group->id);
  }

  public function list(Request $request)
  {
    Login::admin();
    $admin_auth_group = AdminAuthGroup::where('del', 2)->get();
    return Yo::echo([
      'list' => $admin_auth_group
    ]);
  }

  public function select(Request $request)
  {
    Login::admin();
    $admin_auth_group = AdminAuthGroup::where('del', 2)->get();
    return Yo::echo([
      'list' => $admin_auth_group
    ]);
  }
}
