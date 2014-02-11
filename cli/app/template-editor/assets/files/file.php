<?php

  error_reporting(E_ALL);

  ini_set('display_errors', 1);


  require_once '../php/easytemplate.class.php';

  $json = file_get_contents('php://input');

  $request = json_decode($json, true);

  $DEFAULTS = array('imagesrc' => 'assets/images/test.jpg');


  $filename = isset($request["url"]) ? $request["url"] : $request["filename"];


  $template = new EasyTemplate($filename, $request['template']);

    // set output type and disallow caching
  header('Content-Type: application/json; charset=utf-8');
  header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header("Expires: Thu, 25 Feb 1971 00:00:00 GMT"); // Snart ørte-og-børti år siden

  $request['defaults'] = isset($request['defaults']) ? $request['defaults'] : $DEFAULTS;

  $result = $template->render( $request['defaults'] , false, true);

  // public function render ($contents = null, $showsource = false, $toString = false) {
  $result .= "THE END.";
  // error_log(json_encode($result, JSON_PRETTY_PRINT));


  print($result);

?>