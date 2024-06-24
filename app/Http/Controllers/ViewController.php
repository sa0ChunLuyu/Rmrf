<?php

namespace App\Http\Controllers;

use http\Header;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yz;

class ViewController extends Controller
{
  public function home()
  {
    return view('home.home', []);
  }
}
