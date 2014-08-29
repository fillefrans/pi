<?php


  // require_once('../php/pi.php');

  require_once('../php/pi.object.mysql.php');


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

  echo mysql_encode($testObject);

?>