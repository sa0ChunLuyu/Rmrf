<?php

namespace App\Http\Controllers;

use Yo as Yoo;
use Lu;

class YoController extends Controller
{
  public function __invoke()
  {
    return Yoo::echo([
      'app_name' => env('APP_NAME'),
      'datetime' => Lu::date(),
      'data' => request()->all()
    ]);
  }
}
