<?php
function include_content($type, $path = '')
{
  $type_map = [
    "style" => "css",
    "script" => "js",
    "return" => "js",
    "mounted" => "js",
    "options" => "js"
  ];
  if (!$path) {
    $url = explode('?', $_SERVER['REQUEST_URI'])[0];
    $url_arr = explode('/', $url == '/' ? '/home' : $url);
    $true_path = implode('/', $url_arr) . '/' . $url_arr[count($url_arr) - 1] . '.' . $type_map[$type];
    $path = resource_path('views' . $true_path);
  } else {
    $path = resource_path('views/' . $path);
  }
  if (!!is_file($path)) {
    $js_content = file_get_contents($path);
    if ($type == "script") {
      $start = strpos($js_content, "// SCRIPT");
      $end = strpos($js_content, "// SCRIPT END");
      return substr($js_content, $start + 10, $end - $start - 10);
    } elseif ($type == "options") {
      $start = strpos($js_content, "// OPTIONS");
      $end = strpos($js_content, "// OPTIONS END");
      return substr($js_content, $start + 31, $end - $start - 33);
    } elseif ($type == "mounted") {
      $start = strpos($js_content, "// MOUNTED");
      $end = strpos($js_content, "// MOUNTED END");
      return substr($js_content, $start + 11, $end - $start - 11);
    } elseif ($type == "return") {
      $start = strpos($js_content, "// RETURN");
      $end = strpos($js_content, "// RETURN END");
      return substr($js_content, $start + 24, $end - $start - 26);
    } elseif ($type == "style") {
      return $js_content;
    } else {
      return '';
    }
  }

  return '';
}
