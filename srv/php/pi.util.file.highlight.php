<?php

  /* highlight source file with CSS classes and line numbers
     Author: Andy Wrigley (http://means.us.com) */


  header('Content-Type: application/json; charset=utf-8');
  header("Cache-Control: no-cache, must-revalidate");
  header("Expires: Thu, 25 Feb 1971 00:00:00 GMT");

  include('format_javascript.php');


  if(!isset($_REQUEST['file'])) {
    $sourcefile = '../../cli/assets/js/pi.js';
  }
  else {
    $sourcefile = '../../cli/assets/js/' . $_REQUEST['file'] . '.js';
    if(!file_exists($sourcefile)) {
      $sourcefile = '../../cli/assets/js/pi.js';
    }
  }


  $source = file_get_contents($sourcefile);

  ini_set( 'highlight.default',   '"class="highlight default"'  ); 
  ini_set( 'highlight.keyword',   '"class="highlight keyword"'  ); 
  ini_set( 'highlight.string',    '"class="highlight string"'   ); 
  ini_set( 'highlight.html',      '"class="highlight htmlsrc"'  );
  ini_set( 'highlight.comment',   '"class="highlight comment"'  );

  //$source = format_javascript($sourcefile);
  $result = array( 'ok'=>1, 'type'=>'documentFragment', 'data'=>[$source]);

  print(json_encode($result));

?>