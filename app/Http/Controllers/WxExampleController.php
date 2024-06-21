<?php

namespace App\Http\Controllers;

use App\Models\MoreConfig;
use Illuminate\Http\Request;
use Yo;
use WeChatPay\Builder;
use WeChatPay\Crypto\AesGcm;
use WeChatPay\Crypto\Rsa;
use WeChatPay\Formatter;


class WxExampleController extends Controller
{
  public static $mp_instance = false;
  public static $mp_config = false;

  public function builder($config)
  {
    self::$mp_config = $config;
    $merchantPrivateKeyFilePath = 'file://' . self::$mp_config['pem_path'];
    $platformCertificateFilePath = 'file://' . self::$mp_config['cer_path'];
    $merchantId = self::$mp_config['mchid'];
    $merchantPrivateKeyInstance = Rsa::from($merchantPrivateKeyFilePath, Rsa::KEY_TYPE_PRIVATE);
    self::$mp_config['pem_key'] = $merchantPrivateKeyInstance;
    $merchantCertificateSerial = self::$mp_config['cer_num'];
    $platformPublicKeyInstance = Rsa::from($platformCertificateFilePath, Rsa::KEY_TYPE_PUBLIC);
    $platformCertificateSerial = self::$mp_config['v3'];
    self::$mp_instance = Builder::factory([
      'mchid' => $merchantId,
      'serial' => $merchantCertificateSerial,
      'privateKey' => $merchantPrivateKeyInstance,
      'certs' => [
        $platformCertificateSerial => $platformPublicKeyInstance,
      ],
    ]);
  }

  public function create($config)
  {
    $res = false;
    try {
      $post_data = [
        'appid' => self::$mp_config['appid'],
        'mchid' => self::$mp_config['mchid'],
        'description' => $config['description'],
        'out_trade_no' => $config['out_trade_no'],
        'notify_url' => $config['notify_url'],
        'amount' => [
          'total' => $config['total'],
        ],
        'payer' => [
          'openid' => $config['openid']
        ],
        'settle_info' => [
          'profit_sharing' => $config['profit_sharing'],
        ]
      ];
      $resp = self::$mp_instance
        ->v3->pay->transactions->jsapi
        ->post([
          'json' => $post_data,
        ]);
      $res = json_decode($resp->getBody(), true);
    } catch (\Exception $e) {
      if ($e instanceof \GuzzleHttp\Exception\RequestException && $e->hasResponse()) {
        $r = $e->getResponse();
        $res = json_decode($r->getBody(), true);
      }
    }
    $params = [
      'appId' => self::$mp_config['appid'],
      'timeStamp' => (string)time(),
      'nonceStr' => self::nonce(),
      'package' => 'prepay_id=' . $res['prepay_id'],
    ];
    $params += ['paySign' => Rsa::sign(
      Formatter::joinedByLineFeed(...array_values($params)),
      self::$mp_config['pem_key']
    ), 'signType' => 'RSA'];
    return [
      'appid' => $params['appId'],
      'timestamp' => $params['timeStamp'],
      'nonce_str' => $params['nonceStr'],
      'package' => $params['package'],
      'pay_sign' => $params['paySign'],
      'sign_type' => $params['signType'],
    ];
  }

  public static function nonce($l = 16)
  {
    $charts = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz0123456789";
    $max = strlen($charts) - 1;
    $noncestr = "";
    for ($i = 0; $i < $l; $i++) {
      $noncestr .= $charts[rand(0, $max)];
    }
    return $noncestr;
  }

  public function wx_pay(Request $request)
  {
    $appid = $request->post('UNIAPP_APPID');
    $wx_config_data = MoreConfig::where('mark', $appid)->where('type', '微信小程序')->first();
    if (!$wx_config_data) Yo::error_echo(100001, ['参数配置']);
    $wx_config_str = $wx_config_data->config;
    $wx_config = json_decode($wx_config_str, true);
    $pay = $wx_config['pay'][0];
    $pay_config_data = MoreConfig::where('mark', $pay)->where('type', '微信支付')->first();
    $pay_config_str = $pay_config_data->config;
    $pay_config = json_decode($pay_config_str, true);
    self::builder([
      'appid' => $appid,
      'pem_path' => base_path() . $pay_config['Key'],
      'cer_path' => base_path() . $pay_config['Crt'],
      'cer_num' => $pay_config['Number'],
      'mchid' => $pay,
      'v3' => $pay_config['V3'],
    ]);
    $openid = $request->post('openid');
    $out_trade_no = 'test' . time();
    $pay = self::create([
      'description' => '测试',
      'out_trade_no' => $out_trade_no,
      'notify_url' => env('APP_URL') . '/api/Pay/Wxp/callback',
      'total' => 1,
      'openid' => $openid,
      'profit_sharing' => false,
    ]);
    return Yo::echo([
      'info' => $pay
    ]);
  }

  public function mp_login(Request $request)
  {
    $appid = $request->post('UNIAPP_APPID');
    $wx_config_data = MoreConfig::where('mark', $appid)->where('type', '微信小程序')->first();
    if (!$wx_config_data) Yo::error_echo(100001, ['参数配置']);
    $wx_config_str = $wx_config_data->config;
    $wx_config = json_decode($wx_config_str, true);
    $app_secret = $wx_config['AppSecret'];
    $code = $request->post('code');
    $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret=' . $app_secret . '&js_code=' . $code . '&grant_type=authorization_code';
    $info = file_get_contents($url);
    $json = json_decode($info);
    $response = get_object_vars($json);
    if (isset($response['errcode'])) Yo::error_echo(100000, [
      "微信接口报错[{$response['errcode']}]"
    ]);
    if (!isset($response['openid'])) Yo::error_echo(100000, [
      "微信接口调用失败"
    ]);
    return Yo::echo([
      'info' => $response
    ]);
  }

  public function mp_phone(Request $request)
  {
    $appid = $request->post('UNIAPP_APPID');
    $wx_config_data = MoreConfig::where('mark', $appid)->where('type', '微信小程序')->first();
    if (!$wx_config_data) Yo::error_echo(100001, ['参数配置']);
    $wx_config_str = $wx_config_data->config;
    $wx_config = json_decode($wx_config_str, true);
    $app_secret = $wx_config['AppSecret'];
    $code = $request->post('code');
    $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret=' . $app_secret . '&js_code=' . $code . '&grant_type=authorization_code';
    $info = file_get_contents($url);
    $json = json_decode($info);
    $response = get_object_vars($json);
    if (isset($response['errcode'])) Yo::error_echo(100000, [
      "微信接口调用失败[{$response['errcode']}]"
    ]);
    if (!isset($response['openid'])) Yo::error_echo(100000, [
      "微信接口调用失败[openid]"
    ]);
    if (!isset($response['session_key'])) Yo::error_echo(100000, [
      "微信接口调用失败[session_key]"
    ]);
    $session_key = $response['session_key'];
    if (strlen($session_key) != 24) Yo::error_echo(100000, [
      "微信接口参数异常[session_key]"
    ]);
    $aesKey = base64_decode($session_key);
    $iv = $request->post('iv');
    $encrypted_data = $request->post('encrypted_data');
    if (strlen($iv) != 24) Yo::error_echo(100000, [
      "微信接口参数异常[iv]"
    ]);
    $aesIV = base64_decode($iv);
    $aesCipher = base64_decode($encrypted_data);
    $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
    $dataObj = json_decode($result);
    if ($dataObj == NULL) Yo::error_echo(100000, [
      "微信接口参数异常[data]"
    ]);
    if ($dataObj->watermark->appid != $appid) Yo::error_echo(100000, [
      "微信接口参数异常[appid]"
    ]);
    $data = json_decode($result, true);
    if (!isset($data['phoneNumber'])) Yo::error_echo(100000, [
      "微信接口调用失败[phoneNumber]"
    ]);
    return Yo::echo([
      'info' => $data
    ]);
  }
}
