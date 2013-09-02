<?php

  session_start();

  header('Content-Type: application/json; charset=utf-8');
  header("Cache-Control: no-cache, must-revalidate");
  header("Expires: Thu, 25 Feb 1971 00:00:00 GMT");


  if(isset($_REQUEST['filename'])) {
    $filename = $_REQUEST['filename'];
  }


  

  $fp = gzopen($filename, "wb");



  print(json_encode($result));

?>