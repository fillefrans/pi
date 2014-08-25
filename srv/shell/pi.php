#!/usr/bin/php -q
<?php


  // save current directory
  $origlocation = getcwd();

  // run within containing directory
  chdir(__DIR__);



  if ($argc >= 2) {

    // if arg1 is verb/switch, there will be a file named $scriptfile

    $argno = 1;
    $match = null;

    $scriptfile = null;

    while ( ($argno < $argc) || file_exists( ($scriptfile = basename(__FILE__, '.php') . '.' . $argv[$argno++] . '.php')) ) {
      // $scriptfile = basename(__FILE__, '.php') . '.' . $argv[1] . '.php';
      
      print("Looking for : $scriptfile ...");
      if (file_exists($scriptfile)) {
        $match = $argno;
        print(" found it!\n");
      }
      else {
        print(" not found.\n");
      }
    }



    if (file_exists($scriptfile)) {


      // this means arg1 is a verb, so slice off the verb,
      // and pass along the rest of the parameters

      // the remaining command line parameters
      $args = array_slice($argv, $argno);

      $paramstr = implode(' ', $args);

      $safecommandline = escapeshellcmd('php ' . escapeshellarg($scriptfile) . ' ' . $paramstr;

      print("Calling : $safecommandline\n"));

      passthru($safecommandline);
    }
    else {
      print("File does not exist : $scriptfile\n");
    }
  }


  // restore current directory
  chdir($origlocation);


?>