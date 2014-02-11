<?php


define('DEBUG',         true);

define('DATA_DIR',      'tracs/');
define('INPUT_DIR',     DATA_DIR . 'gzin/');
define('OUTPUT_DIR',    DATA_DIR . 'gzout');


define('MAX_FILESIZE',  20480000);


$data = array();

$dictionary = array();



function addtodictionary($chunk) {
  
}





function processdataline($line) {
  $exploded = explode($line, ':', 2);

  $sec=intval($exploded[0]);

  $caption = (count($exploded) > 1) ? trim($exploded[1]) : '');
  


}




function processgzinfile($gzinfile) {

  $contents = gzread(file_get_contents($gzinfile, false, null, -1, MAX_FILESIZE), MAX_FILESIZE);

  foreach ($contents as $line) {
    # code...
    processdataline($line);

  }

}













// do the thing


foreach (glob(INPUT_DIR '*.gz') as $gzinfile) {
  processgzinfile($gzinfile);
}







?>