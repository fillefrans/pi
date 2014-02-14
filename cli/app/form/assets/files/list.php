<?php

  require_once '../php/templatelist.class.php';


  $list = new TemplateList(".");

    // set output type and disallow caching
  header('Content-Type: application/json; charset=utf-8');
  header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header("Expires: Thu, 25 Feb 1971 00:00:00 GMT"); // Snart ørte-og-børti år siden


  $json = $list->toJSON();

  print($json);

?>