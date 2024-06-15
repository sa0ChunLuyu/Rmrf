<?php

namespace App\Http\Controllers;

use App\Http\Request\EditConfig;
use App\Models\Config;
use Illuminate\Http\Request;
use Login;
use Yo;

class ConfigController extends Controller
{
  public function create(EditConfig $request)
  {
    Login::admin(['config-config']);
    $config = new Config();
    $config->name = $request->post('name');
    $config->value = $request->post('value') ?? '';
    $config->type = $request->post('type');
    $config->client = $request->post('client');
    $config->login = $request->post('login');
    $config->remark = $request->post('remark') ?? '';
    $config->save();
    $config = Config::find($config->id);
    if (in_array($config->type, [3, 4, 5])) $config->value = json_decode($config->value, true);
    return Yo::echo([
      'info' => $config
    ]);
  }

  public function update(EditConfig $request)
  {
    Login::admin(['config-config']);
    $config = Config::where('id', $request->post('id'))->first();
    if (!$config) Yo::error_echo(100001, ['配置']);
    $config->name = $request->post('name');
    $config->value = $request->post('value');
    $config->type = $request->post('type');
    $config->client = $request->post('client');
    $config->login = $request->post('login');
    $config->remark = $request->post('remark') ?? '';
    $config->save();
    if (in_array($config->type, [3, 4, 5])) $config->value = json_decode($config->value, true);
    return Yo::echo([
      'info' => $config
    ]);
  }

  public function delete(Request $request)
  {
    Login::admin(['config-config']);
    $config = Config::where('id', $request->post('id'))->first();
    if (!$config) Yo::error_echo(100001, ['配置']);
    $config->delete();
    if (in_array($config->type, [3, 4, 5])) $config->value = json_decode($config->value, true);
    return Yo::delete_echo($config->id);
  }

  public function list()
  {
    Login::admin(['config-config']);
    $config = Config::get();
    foreach ($config as $item) {
      if (in_array($item->type, [3, 4, 5])) $item->value = json_decode($item->value, true);
    }
    return Yo::echo([
      'list' => $config
    ]);
  }

  public function get(Request $request)
  {
    $client = $request->get('client');
    if (!$client) $client = 'public';
    $client_number = 0;
    switch ($client) {
      case 'admin':
        $client_number = 1;
        Login::admin_check();
        break;
    }
    $config_arr = $request->post('config_arr');
    if (!$config_arr) $config_arr = [];
    $configs = $this->getConfigList($config_arr, $client_number);
    return Yo::echo($configs);
  }

  public function getConfigList($arr, $client)
  {
    $config_arr = [];
    foreach ($arr as $item) $config_arr[$item] = '';
    $config_db = Config::whereIn('name', $arr);
    if (!Login::$info) $config_db->where('login', 2);
    if ($client != 0) $config_db->whereIn('client', [0, $client]);
    $config = $config_db->get();
    foreach ($config as $item) {
      $value = $item->value;
      if (in_array($item->type, [3, 4, 5])) {
        $value = json_decode($value, true);
      }
      $config_arr[$item->name] = $value;
    }
    return $config_arr;
  }
}
