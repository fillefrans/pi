<?php


  // require_once('../php/pi.php');

  require_once('../php/pi.object.mysql.php');



  require_once '../php/lib/colors.php';


  echo "PI TEST\n";



  $testObject = new PiObjectMySQL(PiType::STRUCT);

  $testObject->add("test", new PiType(PiType::UINT32));
  $testObject->add("test2", new PiType(PiType::INT32));
  $testObject->add("test3", new PiType(PiType::FLOAT32));

  $testObject->add("test64", new PiType(PiType::FLOAT64));


  $testObject->add("afile", new PiType(PiType::FILE));
  $testObject->add("geo", new PiType(PiType::GEO));

  $testObject->add("date", new PiType(PiType::DATE));
  $testObject->add("time", new PiType(PiType::TIME));
  $testObject->add("timestamp", new PiType(PiType::DATETIME_LOCAL));
  $testObject->add("seconds", new PiType(PiType::UINT8));

  $testObject->add("long", new PiType(PiType::STR, 31));


  $nested = new PiObjectMySQL(PiType::STRUCT);

  $nested->add("nested_date", new PiType(PiType::DATE));
  $nested->add("nested_time", new PiType(PiType::TIME));
  $nested->add("nested_timestamp", new PiType(PiType::DATETIME_LOCAL));
  $nested->add("nested_seconds", new PiType(PiType::UINT8));

  // $testObject->add("NESTED", $nested);



  // echo "mysql_encode() : \n";

    // // Create new Colors class
    // $colors = new Colors();
 
    // // Get Foreground Colors
    // $fgs = $colors->getForegroundColors();
    // // Get Background Colors
    // $bgs = $colors->getBackgroundColors();
 
    // // Loop through all foreground and background colors
    // $count = count($fgs);
    // for ($i = 0; $i < $count; $i++) {
    //   echo $colors->getColoredString("Test Foreground colors", $fgs[$i]) . "\t";
    //   if (isset($bgs[$i])) {
    //     echo $colors->getColoredString("Test Background colors", null, $bgs[$i]);
    //   }
    //   echo "\n";
    // }
    // echo "\n";
 
    // // Loop through all foreground and background colors
    // foreach ($fgs as $fg) {
    //   foreach ($bgs as $bg) {
    //     echo $colors->getColoredString("Test Colors", $fg, $bg) . "\t";
    //   }
    //   echo "\n";
    // }
 


  // echo mysql_encode($testObject);

  // echo json_encode($testObject, JSON_PRETTY_PRINT);
  // var_dump(posix_getpwnam("kroma"));

  // var_dump(posix_getrlimit());
 
  // var_dump(posix_times());
  // var_dump(posix_uname());


  require_once("../php/pi.type.permissions.php");

  $permissions = new PiTypePermissions(0755);

  $permissions->setAll(0755);

  // echo json_encode($permissions, JSON_PRETTY_PRINT);

  require_once("../php/pi.type.user.php");

  // $user = new PiUser("1");

  $cache = new PiCache();

  $user = $cache->read("pi.user.1");

  print("it's a " . get_class($user));
  echo json_encode($user, JSON_PRETTY_PRINT);

  if ($user === false) {
    $user = new PiUser();
    $cache->write("pi.user.1", $user);
  }


  require_once("../php/pi.type.address.php");

  $address = new PiTypeAddress("db:mysql|pi.user.34@viewshq.no");


  print("it's a " . get_class($address));
  echo json_encode($address, JSON_PRETTY_PRINT);


  require_once("../php/pi.type.array.php");

  $arr = new PiTypeArray();
  print("it's a " . get_class($arr));
  echo json_encode($arr, JSON_PRETTY_PRINT);


?>