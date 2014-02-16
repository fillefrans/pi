<?php

  error_reporting(E_ALL);

  ini_set('display_errors', 1);


  require_once '../php/easytemplate.class.php';

  $json = file_get_contents('php://input');

  $request = json_decode($json, true);

  $DEFAULTS = array('imagesrc' => 'assets/images/test.jpg');


  $filename = isset($request["url"]) ? $request["url"] : $request["filename"];


  $template = new EasyTemplate($filename, isset($request['template']) ? $request['template'] : null);

    // set output type and disallow caching
  header('Content-Type: application/json; charset=utf-8');
  header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header("Expires: Thu, 25 Feb 1971 00:00:00 GMT"); // Snart ørte-og-børti år siden

  $request['defaults'] = (isset($request['defaults']) && is_array($request['defaults'])) ? array_merge($DEFAULTS, $request['defaults']) : $DEFAULTS;

  // public function render ($contents = null, $showsource = false, $toString = false) {
  $result = $template->render( $request['defaults'], false, true );


  // if debug flag set in request
  if( isset($request['debug']) && ($request['debug'] == true) ) {
    $result .= "<pre style='text-align:left;max-width:70%;'>\n\nLOG:\n";
    foreach ($template->log as $key => $value) {
      $result .= "$key : $value\n";
    }

    unset($request['template']); // because it's unprintable
    // $result .= "\n\nREQUEST:\n". json_encode($request);

    $result .= "\n\nTHE END.</pre>";
  }


  // error_log(json_encode($result, JSON_PRETTY_PRINT));

  if(strlen($result) === 0) {
    $result = '{ error : "render() returned empty string"}';
  }

  print($result);

?>