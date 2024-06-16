<?php

namespace Workerman\Lib;
class Tool
{
  public static $env = null;

  // region 获取 UUID
  public static function uuid($break = '-'): string
  {
    $chars = md5(uniqid(mt_rand(), true));
    $chars_arr = [
      substr($chars, 0, 8),
      substr($chars, 8, 4),
      substr($chars, 12, 4),
      substr($chars, 16, 4),
      substr($chars, 20, 12),
    ];
    return implode($break, $chars_arr);
  }
  // endregion

  // region 发送POST请求
  public static function post($url, $data = [], $decode = true, $type = 'json')
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
    if ($decode) {
      return json_decode($r, true);
    } else {
      return $r;
    }
  }
  // endregion

  // region 读取 Config ini
  public static function ini($key, $default = false)
  {
    if (!$key) return $default;
    if (!self::$env) {
      $config_file_path = dirname(__DIR__, 2) . '/.env';
      $env_content = file_get_contents($config_file_path);
      $env_ini = parse_ini_string($env_content);
      self::$env = $env_ini;
    }
    return (isset(self::$env[$key])) ? self::$env[$key] : $default;
  }
  // endregion

  // region 10位时间戳 格式化
  public static function date($time = false, $format = "Y-m-d H:i:s")
  {
    if (!$time) $time = time();
    return date($format, $time);
  }
  // endregion


  // region 去除空格
  public static function ge($str)
  {
    return preg_replace("/\s+/", ' ', $str);
  }
  // endregion

  // region 毫秒时间戳
  public static function time()
  {
    return floor(microtime(true) * 1000);
  }
  // endregion
}
