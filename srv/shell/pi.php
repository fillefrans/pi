<?php

  /**
   * Pi Shell Script Bootstrapper for PHP
   * - Changes working dir to pi/srv/shell
   * - Maps switches to individual php files (e.g. "pi update phpmyadmin" => pi/srv/shell/pi.update.phpmyadmin.php)
   * - Passes on arguments
   * - Calls passthru()
   * - Restores working dir
   * 
   * @package pi.shell
   * @version 1.0
   *
   * @author Johan Telstad <jt@enfield.no>
   *
   * @copyright 2011-2014 Views AS
   */


  $DEBUG = true;


  /*  DEBUG  */
  if (!defined('PI_SHELL_DEBUG')) {
    define('PI_SHELL_DEBUG', $DEBUG);
  }


  require_once '../php/lib/colors.php';

  // save current directory
  $origlocation = getcwd();

  // run within containing directory
  chdir(__DIR__);

  $matches = array();

  if ($argc >= 2) {



    for ($i = 1; $i < $argc; $i++) {
      $scriptfile = basename(__FILE__, '.php') . '.' . implode('.', array_slice($argv, 1, $i)) . '.php';
      if (PI_SHELL_DEBUG) {
        // print("looking : $scriptfile\n");
        if (file_exists($scriptfile)) {
          array_push($matches, $scriptfile);
          $match = $i+1;
          // print("found : $scriptfile\n\n");
        }
      }

      $scriptfile = basename(__FILE__, '.php') . '.' . implode('.', array_slice($argv, 1, $i)) . '.sh';
      if (PI_SHELL_DEBUG) {
        // print("looking : $scriptfile\n");
        if (file_exists($scriptfile)) {
          array_push($matches, $scriptfile);
          // print("found : $scriptfile\n\n");
          $match = $i+1;
        }
      }

    }


    if ($matches) {

      $script = array_pop($matches);

      $args = array_slice($argv, $match);

      $paramstr = implode(' ', $args);

      $type = pathinfo($script, PATHINFO_EXTENSION);


      // prefer shell scripts (.sh) over php scripts (.php)
      if ($type == 'php')  {
        if ($script == "pi.php") {
          print("We are calling ourselves!\n");
        }
        $safecommandline = escapeshellcmd('php ' . escapeshellarg($script) . ' ' . $paramstr);
      }
      else {
        $safecommandline = escapeshellcmd(escapeshellarg('./'.$script) . ' ' . $paramstr);
      }


      // if (PI_SHELL_DEBUG) {
      //   print("Calling : $safecommandline\n");
      // }

      passthru($safecommandline);

    }
  }
  else {
    print("too few args ($argc)" . ($argc ? " : " . $argv[0] : "") . "\n");
  }


  // restore current directory
  chdir($origlocation);

  // print("[PI SHELL : FINISHED.]\n");

?>