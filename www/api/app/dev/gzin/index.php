<?php


  define('DEBUG', true);


  require_once( __DIR__ . "/../../../../../srv/php/pi.php");

  error_reporting(E_ALL);




  define('GZOUT',         GZ_ROOT);
  define('MAX_FILESIZE',  20480000);


  // set output type and disallow caching
  header('Content-Type: application/json; charset=utf-8');
  header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header("Expires: Thu, 25 Feb 1971 00:00:00 GMT"); // Snart ørte-og-børti år siden


  function reply($msg, $exitcode=1) {
    exit(json_encode(array("ok" => $exitcode, "msg"=>$msg)));
  }


  if(!DEBUG) {
    if(!isset($_GET['apikey'])) {

      reply("No apikey.", 0);
    }

    if(!isset($_GET['filename'])) {
      reply("No filename.", 0);
    }
  }



  $rawgz = file_get_contents("php://input");


  $gzoutname = GZOUT . $_GET['apikey'] . "." . $_GET['filename'];

  file_put_contents($gzoutname, $rawgz);

  $gunzipped = gzdecode($rawgz, strlen($rawgz));

  file_put_contents("gzin.log", $gunzipped, FILE_APPEND);


  reply("success.");


?>