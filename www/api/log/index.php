<?php

  error_reporting(0);

  ini_set('display_errors', 0);

  define('LOG', 'pi.log.jsonstrings');
  define('ERROR_LOG', 'pi.errorlog');

    // set output type and disallow caching
  header('Content-Type: application/json; charset=utf-8');
  header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header("Expires: Thu, 25 Feb 1971 00:00:00 GMT"); // Snart ørte-og-børti år siden


  $json = file_get_contents('php://input');
    

  try {
    
    $data = json_decode($json, true);

    $line = array();

    $line['request']    = $data;
    $line['referrer']   = $_SERVER['HTTP_REFERER'];
    $line['browser']    = $_SERVER['HTTP_USER_AGENT'];
    $line['timestamp']  = time();

    $output = json_encode($line);

    file_put_contents(LOG, $output."\n", FILE_APPEND);

  }
  catch (Exception $e) {

    $data = array();

    $data['request']    = base64_encode($json);
    $data['error']      = get_class($e);
    $data['message']    = $e->getMessage();
    $data['referrer']   = $_SERVER['HTTP_REFERER'];
    $data['browser']    = $_SERVER['HTTP_USER_AGENT'];
    $data['timestamp']  = time();

    file_put_contents(ERROR_LOG, json_encode($data)."\n", FILE_APPEND);

  }

?>