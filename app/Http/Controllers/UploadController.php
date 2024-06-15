<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Lu;
use Yo;
use Login;

class UploadController extends Controller
{
  public function search()
  {
    $ext = Upload::select('ext')->groupBy('ext')->get();
    $from = ['AdminImage'];
    return Yo::echo([
      'ext' => $ext,
      'from' => $from,
    ]);
  }

  public function list(Request $request)
  {
    Login::admin(['config-upload']);
    $search = $request->post('search');
    $time = $request->post('time');
    $start_time = !!$time[0] ? Lu::date(strtotime($time[0] . ' 00:00:00')) : '';
    $end_time = !!$time[1] ? Lu::date(strtotime($time[1] . ' 23:59:59')) : '';
    $ext = $request->post('ext');
    $from = $request->post('from');
    $from_map = [
      'AdminImage' => '/api/Admin/Upload/image',
    ];
    $from_search = '';
    if (!!$from) $from_search = $from_map[$from];
    $upload_list = Upload::where(function ($query) use ($search) {
      if ($search != '') $query->where('uuid', $search)
        ->orWhere('name', $search)
        ->orWhere('md5', $search);
    })
      ->where(function ($query) use ($start_time) {
        if ($start_time != '') $query->where('created_at', '>=', $start_time);
      })
      ->where(function ($query) use ($end_time) {
        if ($end_time != '') $query->where('created_at', '<=', $end_time);
      })
      ->where(function ($query) use ($ext) {
        if ($ext != '') $query->where('ext', $ext);
      })
      ->where(function ($query) use ($from_search) {
        if ($from_search != '') $query->where('from', $from_search);
      })
      ->orderBy('id', 'desc')
      ->paginate(20);
    return Yo::echo([
      'list' => $upload_list
    ]);
  }

  public function delete(Request $request)
  {
    Login::admin(['config-upload']);
    $id = $request->post('id');
    $upload = Upload::where('id', $id)->first();
    if (!$upload) Yo::error_echo(100001, ['上传文件']);
    $upload->delete();
    unlink($upload->path);
    return Yo::delete_echo($upload->id);
  }

  public function image(Request $request)
  {
    $base64 = $request->post('base64');
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)) {
      $type = ['png', 'jpeg', 'jpg', 'gif'];
      if (!in_array($result[2], $type)) Yo::error_echo(100015);
      $md5 = md5($base64);
      $upload = Upload::where('md5', $md5)->first();
      if (!$upload) {
        $disk = Storage::disk('public');
        $name = Str::orderedUuid();
        $date = date('Y/m');
        $path = "/assets/upload/image/$date/$name.$result[2]";
        $put = $disk->put($path, base64_decode(str_replace($result[1], '', $base64)));
        if (!$put) Yo::error_echo(100016, ['put']);
        $save = "/storage/assets/upload/image/$date/$name.$result[2]";
        $size = $disk->size($path);
        $p = $disk->path($path);
        $upload = new Upload();
        $upload->uuid = $name;
        $upload->name = 'Base64-' . $md5;
        $upload->path = $p;
        $upload->url = $save;
        $upload->from = explode('?', $_SERVER['REQUEST_URI'])[0];
        $upload->size = $size / 1024 / 1024;
        $upload->ext = $result[2];
        $upload->md5 = $md5;
        $upload->save();
      }
      return Yo::echo([
        'url' => $upload->url
      ]);
    } else {
      Yo::error_echo(100016, ['base64']);
    }
  }
}
