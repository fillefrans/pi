<?php


define('DEBUG', true);


require_once( __DIR__ . "/../pi.php");

error_reporting(E_ALL);



define('GZIN',          GZ_ROOT);

define('DATA_DIR',      'tracs/');
define('OUTPUT_DIR',    DATA_DIR . 'gzout');


define('MAX_FILESIZE',  20480000);

define('MAX_LINE_LENGTH', 1024);




$data = array();

$dictionary = array();


function gzfile_get_contents($filename, $use_include_path = 0) { 
    $file = gzopen($filename, 'rb', $use_include_path); 
    $data = null;
    if ($file) { 
        $data = [];
        while (!gzeof($file)) { 
            $data[] = gzgets($file, MAX_LINE_LENGTH); 
        } 
        gzclose($file); 
    } 
    return $data; 
} 



function addtodictionary($chunk) {
  
}





function processdataline($line) {
  $sepPos = strpos($line, ":");
  if($sepPos===false) {
    return;
  }
  $sec = intval(trim(substr( $line, 0, $sepPos )), 10);
  if(!$sec) {
    return;
  }

  $caption = trim(substr( $line, $sepPos+1 ));

  $project = "";
  $file = "";
  $app = "";


  $appPos = strpos($caption, ") - Sublime Text 2");

  if($appPos) {
    $app = "Sublime Text 2";
    $caption = substr($caption, 0, $appPos);
    $projPos = strrpos($caption, "(");

    if($projPos > 5) {
      $project = substr($caption, $projPos+1);
      $caption = trim(substr($caption, 0, $projPos+1));
      $file = trim($caption);
    }
    else {

    }

  print("project: $project >  time: ".$sec."s >  file: $file\n");
  // print("app: $app >  project: $project >  time: ".$sec."s >  file: $file\n");
  }



}




function processgzinfile($gzinfile) {


  $contents = gzfile_get_contents($gzinfile);

  if(is_array($contents)) {
    foreach ($contents as $line) {
      processdataline($line);
    }
  }
  else {
    print("not array : \n" . json_encode($contents, JSON_PRETTY_PRINT));
  }

}













// do the thing


foreach (glob(GZIN . '*.gz') as $gzinfile) {
  print("\nprocessing: $gzinfile");
  processgzinfile($gzinfile);
}



?>