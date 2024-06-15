<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;

class Yo
{
  public static function create_echo($id = 0)
  {
    return Lu::echo(config('code.200.c'), 200, ['id' => $id]);
  }

  public static function delete_echo($id = 0)
  {
    return Lu::echo(config('code.200.d'), 200, ['id' => $id]);
  }

  public static function update_echo($id = 0)
  {
    return Lu::echo(config('code.200.u'), 200, ['id' => $id]);
  }

  public static function error_echo($code, $replace = [])
  {
    $msg = config("code.{$code}");
    if (count($replace)) $msg = Str::replaceArray('?', $replace, $msg);
    throw new HttpResponseException(Lu::echo($msg, $code));
  }

  public static function debug($data)
  {
    throw new HttpResponseException(Lu::echo('Debug', 100000, $data));
  }

  public static function echo($data = [])
  {
    return Lu::echo(config('code.200.r'), 200, $data);
  }
}
