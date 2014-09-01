<?php


  // require_once('../php/pi.php');

  require_once('../php/pi.db.php');




  $mysql = new PiDB();


  function listdb($which = null) {
    global $mysql;

    if ($which === null) {
      $query = "SHOW DATABASES;";
    }
    else {
      $query = "SHOW TABLES;";
    }


    print("running query : $query\n");

    if ($result = $mysql->query($query)) {
      if ($result instanceof mysqli_result) {
        $data = $mysql->fetch();
        print("its a " . get_class($result));
      }
      else {
        $data = $result;
        print("ooo, its a " . get_class($result));
      }

      print("great success : ");
      var_dump($data);
    }
    else {
      print("great scott!");
      $mysql->error();

    }
  }


  function listtable($which = null) {
    global $mysql;
  }



  if ($argc >= 2) {

    $who    = $argv[1];
    $what   = isset($argv[2]) ? $argv[2] : null;
    $which  = isset($argv[3]) ? $argv[3] : null;;


    $result = $mysql->info($who, $what, $which);

    if ($result) {
      var_dump($result);
      }
    }


?>