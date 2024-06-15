<?php

namespace App\Http\Controllers;

use App\Http\Request\EditAdminAuth;
use App\Models\AdminAuth;
use App\Models\AdminAuthGroup;
use Illuminate\Http\Request;
use Login;
use Yo;

class AdminAuthController extends Controller
{
  public function create(EditAdminAuth $request)
  {
    Login::admin(['config-router']);
    $name = $request->post('name');
    $check_info = AdminAuth::where('name', $name)->where('del', 2)->first();
    if (!!$check_info) Yo::error_echo(100033);
    $admin_auth = new AdminAuth();
    $admin_auth->name = $name;
    $admin_auth->title = $request->post('title');
    $admin_auth->icon = $request->post('icon') ?? '';
    $pid = $request->post('pid');
    $type = $request->post('type');
    if ($type == 1 && $pid != 0) Yo::error_echo(100029);
    if ($type == 2 && $pid != 0) {
      $p_info = AdminAuth::where('id', $pid)->where('pid', 0)->where('type', 1)->first();
      if (!$p_info) Yo::error_echo(100001, ['组路由']);
    }
    $admin_auth->pid = $pid;
    $admin_auth->type = $type;
    $admin_auth->check = $request->post('check');
    $admin_auth->show = $request->post('show');
    $admin_auth->status = $request->post('status');
    $admin_auth->message = $request->post('message') ?? '';
    $admin_auth->order = $request->post('order');
    $admin_auth->save();
    $admin_auth = AdminAuth::where('id', $admin_auth->id)->first();
    return Yo::create_echo($admin_auth->id);
  }

  public function update(EditAdminAuth $request)
  {
    Login::admin(['config-router']);
    $id = $request->post('id');
    $admin_auth = AdminAuth::where('id', $id)->where('del', 2)->first();
    if (!$admin_auth) Yo::error_echo(100001, ['路由']);
    $name = $request->post('name');
    $check_info = AdminAuth::where('name', $name)->where('id', '!=', $id)->where('del', 2)->first();
    if (!!$check_info) Yo::error_echo(100033);
    $admin_auth->name = $request->post('name');
    $admin_auth->title = $request->post('title');
    $admin_auth->icon = $request->post('icon') ?? '';
    $pid = $request->post('pid');
    $type = $request->post('type');
    if ($type == 1 && $pid != 0) Yo::error_echo(100029);
    if ($type == 2 && $pid != 0) {
      $p_info = AdminAuth::where('id', $pid)->where('pid', 0)->where('type', 1)->first();
      if (!$p_info) Yo::error_echo(100001, ['组路由']);
    }
    $admin_auth->pid = $pid;
    $admin_auth->type = $type;
    $admin_auth->check = $request->post('check');
    $admin_auth->show = $request->post('show');
    $admin_auth->status = $request->post('status');
    $admin_auth->message = $request->post('message') ?? '';
    $admin_auth->order = $request->post('order');
    $admin_auth->save();
    $admin_auth = AdminAuth::where('id', $admin_auth->id)->first();
    return Yo::update_echo($admin_auth->id->id);
  }

  public function delete(Request $request)
  {
    Login::admin(['config-router']);
    $admin_auth = AdminAuth::where('id', $request->post('id'))->where('del', 2)->first();
    if (!$admin_auth) Yo::error_echo(100001, ['路由']);
    $son_count = AdminAuth::where('pid', $admin_auth->id)->where('del', 2)->count();
    if ($son_count != 0) Yo::error_echo(100030);
    $admin_auth->del = 1;
    $admin_auth->save();
    return Yo::delete_echo($admin_auth->id);
  }

  public function list(Request $request)
  {
    Login::admin(['config-router']);
    $auth_group = AdminAuth::where('pid', 0)->where('type', 1)->where('del', 2)->orderBy('order', 'desc')->get();
    $list = [];
    foreach ($auth_group as $item) {
      $data = [
        'info' => $item,
      ];
      $auth_group_list = AdminAuth::where('pid', $item->id)->where('type', 2)->where('del', 2)->orderBy('order', 'desc')->get();
      $data['list'] = $auth_group_list;
      $list[] = $data;
    }
    $auth_group_single = AdminAuth::where('pid', 0)->where('type', 2)->where('del', 2)->orderBy('order', 'desc')->get()->toArray();
    return Yo::echo([
      'list' => array_merge($list, $auth_group_single)
    ]);
  }

  public function select(Request $request)
  {
    Login::admin(['config-router']);
    $auth_group = AdminAuth::where('pid', 0)->where('type', 1)->where('del', 2)->orderBy('order', 'desc')->get();
    return Yo::echo([
      'list' => $auth_group
    ]);
  }

  public function choose()
  {
    Login::admin();
    $auth_group = AdminAuth::where('pid', 0)->where('type', 1)->where('del', 2)->orderBy('order', 'desc')->get();
    $list = [];
    foreach ($auth_group as $item) {
      $data = [
        'info' => $item,
      ];
      $auth_group_list = AdminAuth::where('pid', $item->id)->where('type', 2)->where('check', 1)->where('del', 2)->orderBy('order', 'desc')->get();
      $data['list'] = $auth_group_list;
      if (count($data['list']) == 0) continue;
      $list[] = $data;
    }
    $auth_group_single = AdminAuth::where('pid', 0)->where('type', 2)->where('check', 1)->where('del', 2)->orderBy('order', 'desc')->get();
    if (count($auth_group_single) != 0) {
      $list[] = [
        'info' => [
          'id' => 0,
          'title' => '未分组',
        ],
        'list' => $auth_group_single
      ];
    }
    return Yo::echo([
      'list' => $list
    ]);
  }

  public function menu()
  {
    Login::admin();
    $menu_group = AdminAuth::select('id', 'name', 'title', 'icon', 'status', 'type')
      ->where('pid', 0)->where('show', 1)->where('del', 2)
      ->orderBy('order', 'desc')->get();
    $list = [];
    foreach ($menu_group as $item) {
      if ($item->type == 2) {
        $list[] = [
          "id" => $item->id,
          "name" => $item->name,
          "title" => $item->title,
          "icon" => $item->icon,
          "status" => $item->status,
          "children" => []
        ];
      } else {
        switch (Login::$info->admin_auth_group) {
          case -1:
            $auth_list = AdminAuth::select('id', 'name', 'title', 'icon', 'status')->where('pid', $item->id)
              ->where('type', 2)->where('show', 1)->where('status', 1)->where('del', 2)
              ->orderBy('order', 'desc')->get();
            break;
          case 0:
            $auth_list = AdminAuth::select('id', 'name', 'title', 'icon', 'status')->where('pid', $item->id)
              ->where('type', 2)->where('check_type', 1)->where('show', 1)->where('status', 1)->where('del', 2)
              ->orderBy('order', 'desc')->get();
            break;
          default:
            $admin_auth = AdminAuthGroup::find(Login::$info->admin_auth_group);
            $auths = json_decode($admin_auth->admin_auths, true);
            $auth_list = AdminAuth::select('id', 'name', 'title', 'icon', 'status')
              ->where(function ($query) use ($auths, $item) {
                $query->whereIn('id', $auths)->where('pid', $item->id)->where('type', 2)->where('check', 1)->where('show', 1)->where('status', 1)->where('del', 2);
              })
              ->orWhere(function ($query) use ($auths, $item) {
                $query->where('type', 2)->where('pid', $item->id)->where('check', 2)->where('show', 1)->where('status', 1)->where('del', 2);
              })
              ->orderBy('order', 'desc')->get();
        }
        if (count($auth_list) !== 0) {
          $list[] = [
            "id" => $item->id,
            "name" => $item->name,
            "title" => $item->title,
            "icon" => $item->icon,
            "status" => $item->status,
            "children" => $auth_list
          ];
        }
      }
    }
    return Yo::echo([
      'list' => $list
    ]);
  }
}
