<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Lu
{

  public static $request = null;
  public static $spend = 0;
  public static $path = '';

  public static function CheckTableName()
  {
    $table_name = 'zz_request_log_' . date('ym');
    $table_count = DB::select('select count(1) as c from information_schema.TABLES where table_schema = ? and table_name = ?', [env('DB_DATABASE'), $table_name])[0];
    if ($table_count->c === 0) {
      Schema::create($table_name, function (Blueprint $table) {
        $table->id();
        $table->string('uuid', 50)->index();
        $table->string('token', 50)->index();
        $table->string('ip', 15)->index();
        $table->string('url', 300)->index();
        $table->string('method', 10);
        $table->longtext('params');
        $table->tinyInteger('type')->comment('1-文字 2-文件');
        $table->longtext('input');
        $table->longtext('header');
        $table->string('code', 10)->nullable();
        $table->text('result')->nullable();
        $table->decimal('spend', 6, 3)->nullable();
        $table->timestamps();
      });
    }
    self::$request = new \App\Models\RequestLog;
    self::$request->setTable($table_name);
  }

  public static function RequestLog()
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'OPTIONS' && env('REQUEST_LOG') && !self::$request) {
      $request_url = explode('?', $_SERVER['REQUEST_URI'])[0];
      if (str_contains($request_url, 'WanLiu')) {
        self::CheckTableName();
        self::$spend = self::time();
        $token = '';
        if (!!request()->header('Authorization')) {
          $token_arr = explode('Bearer ', request()->header('Authorization'));
          $token = $token_arr[1] ?? '';
        }
        $uuid = Str::orderedUuid();
        $date = date('Y/m/d');
        self::$path = "log/$date/$uuid.txt";
        self::$request->uuid = $uuid;
        self::$request->token = $token;
        self::$request->ip = self::ip();
        self::$request->url = $request_url;
        self::$request->method = $_SERVER['REQUEST_METHOD'];
        $type = 1;
        $input_data = !!request()->post() ? json_encode(request()->post(), JSON_UNESCAPED_UNICODE) : '{}';
        $str_len = mb_strlen($input_data);
        $str_size = $str_len / 1024;
        if ($str_size > 40) $type = 2;
        $params_data = !!$_GET ? json_encode($_GET, JSON_UNESCAPED_UNICODE) : '{}';
        $header_data = !!request()->header() ? self::transformedHeaders() : '{}';
        $str_len = mb_strlen($header_data);
        $str_size = $str_len / 1024;
        if ($str_size > 40) $type = 2;
        self::$request->input = $input_data;
        self::$request->params = $params_data;
        self::$request->header = $header_data;
        self::$request->type = $type;
        self::$request->save();
      }
    }
  }

  public static function transformedHeaders()
  {
    $header_data = request()->header();
    $header = [];
    foreach ($header_data as $key => $header_datum) {
      if (count($header_datum) == 1) {
        $header[$key] = $header_datum[0];
      } else {
        $header[$key] = $header_datum;
      }
    }
    return json_encode($header, JSON_UNESCAPED_UNICODE);
  }

  public static function ip()
  {
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
      $ip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
      $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
      $ip = getenv('REMOTE_ADDR');
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    $res = preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches [0] : '';
    return $res;
  }

  public static function date($time = false, $format = "Y-m-d H:i:s")
  {
    if (!$time) $time = time();
    return date($format, $time);
  }

  public static function time()
  {
    return floor(microtime(true) * 1000);
  }

  public static function exit($data = [])
  {
    $res = $data;
    if ($_SERVER['REQUEST_METHOD'] !== 'OPTIONS' && env('REQUEST_LOG') && !!self::$request) {
      $data_str = !!$data ? json_encode($data, JSON_UNESCAPED_UNICODE) : '{}';
      $str_len = strlen($data_str);
      $str_size = $str_len / 1024;
      $type = self::$request->type;
      if ($str_size > 40) $type = 2;
      if ($type == 2) {
        $input_data = self::$request->input;
        $header_data = self::$request->header;
        $disk = Storage::disk('local');
        $disk->append(self::$path, "POST:
$input_data
-------------------------------
HEADER:
$header_data
-------------------------------
RESULT:
$data_str");
        self::$request->input = self::$path;
        self::$request->header = self::$path;
        self::$request->result = self::$path;
        self::$request->type = 2;
      } else {
        self::$request->result = $data_str;
      }
      self::$request->code = (isset($data['code']) && !!$data['code']) ? $data['code'] : 0;
      self::$request->spend = (self::time() - self::$spend) / 1000;
      self::$request->save();
    }
    return response()->json($res)->setEncodingOptions(JSON_UNESCAPED_UNICODE);
  }

  public static function echo($message = '', $code = 200, $data = [])
  {
    $return = [];
    $return['code'] = intval($code);
    if ($message) $return['message'] = $message;
    if ($data) $return['data'] = $data;
    return self::exit($return);
  }

  public static function post($url, $data, $type = 'json')
  {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    if ($type === 'data') {
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    }
    if ($type === 'json') {
      $data_string = json_encode($data, JSON_UNESCAPED_UNICODE);
      curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json; charset=utf-8',
        'Content-Length: ' . strlen($data_string)
      ]);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    }
    $r = curl_exec($curl);
    curl_close($curl);
    return $r;
  }
}
