<?php
/**
 *  //TODO
 * DateTime: 2019/8/28 13:52
 * File: start.php
 */

use Workerman\Worker;

date_default_timezone_set('Asia/Shanghai');
require_once "workerman/Autoloader.php";
\Workerman\Autoloader::setRootPath(__DIR__);
$config = [];
$config['workerman']['pidFile'] = __DIR__ . '/logs/workerman.pid';
$config['workerman']['logFile'] = __DIR__ . '/logs/workerman.log';
Worker::$pidFile = $config['workerman']['pidFile'];
Worker::$logFile = $config['workerman']['logFile'];
foreach (glob(__DIR__ . '/bot/bot_*.php') as $start_file) {
    require_once $start_file;
}
Worker::runAll();
