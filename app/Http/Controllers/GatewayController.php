<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use GatewayClient\Gateway as Socket;
use Yo;

Socket::$registerAddress = '127.0.0.1:4001';

class GatewayController extends Controller
{
  public function test(Request $request)
  {
    $client = $request->post('client');
    $data = $request->post('data');
    Socket::sendToClient($client, json_encode($data, JSON_UNESCAPED_UNICODE));
    return Yo::echo();
  }

  public function quit(Request $request)
  {
    $client = $request->post('client');
    return Yo::echo([
      'client' => $client
    ]);
  }
}
