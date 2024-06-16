<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */

//declare(ticks=1);
require_once __DIR__ . '/../../Applications/Lib/Tool.php';
require_once __DIR__ . '/../../Applications/Lib/Db.php';
require_once __DIR__ . '/../../Applications/Lib/Db2.php';
date_default_timezone_set(Tool::ini('TIMEZONE'));

use \GatewayWorker\Lib\Gateway;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
  /**
   * 当客户端连接时触发
   * 如果业务不需此回调可以删除onConnect
   *
   * @param int $client_id 连接id
   */
  public static function onConnect($client_id)
  {
    echo json_encode(['CONNECT', date('Y-m-d H:i:s'), $client_id], JSON_UNESCAPED_UNICODE) . "\n";
    Gateway::sendToClient($client_id, json_encode([
      'action' => 'init',
      'client_id' => $client_id
    ], JSON_UNESCAPED_UNICODE));
  }

  /**
   * 当客户端发来消息时触发
   * @param int $client_id 连接id
   * @param mixed $message 具体消息
   */
  public static function onMessage($client_id, $message)
  {
    // $db = Db::get();
    if ($message == Tool::ini('GATEWAY_PING')) {
      echo json_encode(['PING', date('Y-m-d H:i:s'), $client_id], JSON_UNESCAPED_UNICODE) . "\n";
      Gateway::sendToClient($client_id, Tool::ini('GATEWAY_PANG'));
    }
  }

  /**
   * 当用户断开连接时触发
   * @param int $client_id 连接id
   */
  public static function onClose($client_id)
  {
    echo json_encode(['CLOSE', date('Y-m-d H:i:s'), $client_id], JSON_UNESCAPED_UNICODE) . "\n";
    if (!!Tool::ini('GATEWAY_CLOSE')) self::post(Tool::ini('APP_URL') . Tool::ini('GATEWAY_CLOSE'), ['client' => $client_id]);
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
