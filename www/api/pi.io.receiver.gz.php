<?php


  define('DEBUG',         true);


    require_once( __DIR__ . "/../../srv/php/pi.php");



  define('GZOUT', GZ_ROOT);


  define('MAX_FILESIZE',  20480000);


  // set output type and disallow caching
  header('Content-Type: application/json; charset=utf-8');
  header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header("Expires: Thu, 25 Feb 1971 00:00:00 GMT"); // Snart ørte-og-børti år siden



  $get = array();

  parse_str($_SERVER['REQUEST_URI'], $get);

  if(!isset($get['apikey'])) {
    exit;
  }

  if(!isset($get['filename'])) {
    exit;
  }


  $rawgz = file_get_contents("php://input");


  $gzoutname = GZOUT . $get['apikey'] . "." . basename($get['filename'], ".tar.gz") . ".gz";

  file_put_contents($gzoutname, $rawgz);

  $gunzipped = gzdecode($rawgz, strlen($rawgz));

  file_put_contents(basename(__FILE__, ".php") . ".log", $gunzipped, FILE_APPEND);




?>