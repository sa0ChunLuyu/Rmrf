<?php

namespace App\Http\Controllers;

use App\Http\Request\EditIpPool;
use App\Models\IpPool;
use Illuminate\Http\Request;
use Login;
use Yo;

class IpPoolController extends Controller
{
  public function create(EditIpPool $request)
  {
    Login::admin(['config-ip']);
    $ip = $request->post('ip');
    if (!filter_var($ip, FILTER_VALIDATE_IP)) Yo::error_echo(100040);
    $region = $request->post('region');
    $ip_pool = new IpPool();
    $ip_pool->ip = $ip;
    $ip_pool->region = $region;
    $ip_pool->save();
    return Yo::create_echo($ip_pool->id);
  }

  public function update(EditIpPool $request)
  {
    Login::admin(['config-ip']);
    $id = $request->post('id');
    $ip = $request->post('ip');
    if (!filter_var($ip, FILTER_VALIDATE_IP)) Yo::error_echo(100040);
    $region = $request->post('region');
    $ip_pool = IpPool::find($id);
    if (!$ip_pool) Yo::error_echo(100001, ['IP']);
    $ip_pool->ip = $ip;
    $ip_pool->region = $region;
    $ip_pool->save();
    return Yo::update_echo($ip_pool->id);
  }

  public function delete(Request $request)
  {
    Login::admin(['config-ip']);
    $id = $request->post('id');
    $ip_pool = IpPool::find($id);
    if (!$ip_pool) Yo::error_echo(100001, ['IP']);
    $ip_pool->delete();
    return Yo::delete_echo($ip_pool->id);
  }

  public function list(Request $request)
  {
    Login::admin(['config-ip']);
    $search = $request->post('search');
    $list = IpPool::where(function ($query) use ($search) {
      if ($search != '') $query->where('ip', 'like', "%$search%")->orWhere('region', 'like', "%$search%");
    })->orderBy('id', 'desc')->paginate(20);
    return Yo::echo([
      'list' => $list
    ]);
  }
}
