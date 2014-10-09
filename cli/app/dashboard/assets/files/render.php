<?php

  error_reporting(E_ALL);

  ini_set('display_errors', 1);


  $json = file_get_contents('php://input');

  $request = json_decode($json, true);

  $DEFAULTS = array('imagesrc' => 'assets/images/test.jpg');


  $filename = isset($request["url"]) ? $request["url"] : $request["filename"];


    // set output type and disallow caching
  header('Content-Type: application/json; charset=utf-8');
  header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header("Expires: Thu, 25 Feb 1971 00:00:00 GMT"); // Snart ørte-og-børti år siden

  $request['defaults'] = (isset($request['defaults']) && is_array($request['defaults'])) ? array_merge($DEFAULTS, $request['defaults']) : $DEFAULTS;

  // error_log(json_encode($result, JSON_PRETTY_PRINT));


  function toRegex ($keyArray) {

    if(!is_array($keyArray)) {
      return null;
    }

    $i = count($keyArray)-1;

    while($i >= 0) {
      // regex to match {key*}
      $keyArray[$i] = "/\{(" . $keyArray[$i] . ")([^}]*)\}/";
      $i--;
    }
    return $keyArray;
  }





  function render ($filename, $contents = null) {

    $rendered = "";
    $raw = file_get_contents("../../" . $filename);

    if (!$raw) {
      return json_encode(array("error" => "no file: $filename"));
    }
    $defaults = array();
    $data = array('adwidth' => 640, 'adheight' => 180);

    $adSize = getSizeFromFilename($filename);

    if(count($adSize) >= 2) {
      $data['adwidth']  = $adSize['w'];
      $data['adheight'] = $adSize['h'];
    } 


    if($contents && is_array($contents)) {

      foreach ($defaults as $key => $value) {
        if(!isset($data[$key]) && (!is_numeric($key))) {
          $data[$key] = $value;
        }
      }
      // $contents = array_merge($defaults, $contents);

      foreach ($contents as $key => $value) {
        $data[$key] = $value;
      }
    }


    $keys   = toRegex(array_keys($data));
    $values = array_values($data);

    $rendered = preg_replace($keys, $values, $raw);

    return $rendered;
  }



  $result = render($filename);


  if(strlen($result) === 0) {
    $result = '{ error : "render() returned empty string"}';
  }



  print($result);



      // $this->longname = str_replace(".html", "", self::folderToKey($filename));
      // $this->jsonfile = str_replace(".html", ".json", $filename);
      // $this->basename = basename($filename, ".html");



  function getSizeFromFilename ($filename) {
    if( strpos($filename, 'x') === false ) {
      return null;
    }

    $size = array('w' => 640, 'h' => 180);

    $nameParts = explode("x", basename($filename, ".html"),2);

    if(count($nameParts)>=2) {
      $size['w'] = intval($nameParts[0], 10);
      $size['h'] = intval($nameParts[1], 10);
    }

    return $size;
  }



?>