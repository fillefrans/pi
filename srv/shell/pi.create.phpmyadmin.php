#!/usr/bin/php -q
<?php


  if ($argc >= 2) {

    // if arg1 is verb/switch, there will be a file named $scriptfile
    $scriptfile = basename(__FILE__, '.php') . '.' . $argv[1] . '.php';


    if (file_exists($scriptfile)) {


      // this means arg1 is a verb, so slice off the verb,
      // and pass along the rest of the parameters

      // the remaining command line parameters
      $args = array_slice($argv, 2);

      $paramstr = implode(' ', $args);

      $safecommandline = escapeshellcmd('php ' . escapeshellarg($scriptfile) . ' ' . $paramstr;

      print("Calling : $safecommandline\n"));

      passthru($safecommandline);
    }
    else {
      print("File does not exist : $scriptfile\n");
    }
  }

?>