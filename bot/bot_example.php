<?php

namespace Workerman\Lib;

use Workerman\Worker;

$bot_loop = new Worker();
$bot_loop->count = 1;
$bot_loop->name = Tool::ini('APP_NAME');

function ExampleFunc()
{
  $db = Db::get();
  $config = $db->getRow('select * from configs where id = ?', [1]);
  var_dump($config);
}

$bot_loop->onWorkerStart = function () {
  ExampleFunc();
  Timer::add(3, function () {
    ExampleFunc();
  });
};
