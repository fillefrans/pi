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

    // Create new Colors class
    $colors = new Colors();
 
    // Get Foreground Colors
    $fgs = $colors->getForegroundColors();
    // Get Background Colors
    $bgs = $colors->getBackgroundColors();
 
    // Loop through all foreground and background colors
    $count = count($fgs);
    for ($i = 0; $i < $count; $i++) {
      echo $colors->getColoredString("Test Foreground colors", $fgs[$i]) . "\t";
      if (isset($bgs[$i])) {
        echo $colors->getColoredString("Test Background colors", null, $bgs[$i]);
      }
      echo "\n";
    }
    echo "\n";
 
    // Loop through all foreground and background colors
    foreach ($fgs as $fg) {
      foreach ($bgs as $bg) {
        echo $colors->getColoredString("Test Colors", $fg, $bg) . "\t";
      }
      echo "\n";
    }
 


  // echo mysql_encode($testObject);

  var_dump(posix_uname());

?>