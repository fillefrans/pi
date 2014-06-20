<?php

  /**
   * @package     pi
   * @subpackage  tracs.incoming
   */


  define('DEBUG', true);
  error_reporting(E_ALL);

  ini_set('display_errors', 1);

  require_once( __DIR__ . "/../pi.php");

//     define('UPLOAD_ROOT',       SRV_ROOT . "data/upload/");
//     define('GZ_ROOT',           UPLOAD_ROOT . "gz/");
  define('GZIN',            GZ_ROOT);

  define('DATA_DIR',        'tracs/');
  define('OUTPUT_DIR',      DATA_DIR . 'gzout');
  define('MAX_FILESIZE',    20480000);
  define('MAX_LINE_LENGTH', 1024);

  define('TRIM_CHARS',      " \t\n\r\0\x0B()");

  $data     = array();
  $summary  = array();

  $reportstart = 0;
  $reportend = 0;


  $db = array();



  $mysqli = new MySQLi("localhost", "psmith", "pshrimp", "psmith");

  /* check connection */
  if ($mysqli->connect_errno) {
      printf("MySQL connect failed: %s\n", $mysqli->connect_error);
      exit();
  }


  /* allow local infile */
  if ($mysqli->options(MYSQLI_OPT_LOCAL_INFILE, true)) {
      print("Option value set\n");
  }
  else {
    die("Unable to set option MYSQLI_OPT_LOCAL_INFILE\n");
  }




  function hasgzfile($infile) {
    global $mysqli;

    $fileId = -1;

    $infile = $mysqli->real_escape_string($infile);

    /* If we have to retrieve large amount of data we use MYSQLI_USE_RESULT */
    if (false !== ($result = $mysqli->query("SELECT * FROM `tracs-processed` WHERE gzfile = '$infile';"))) {

      if($result->num_rows === 1) {
        $result->close();
        return true;
      }
    }
    else {
      print(date("H:i:s", time()) . ": \nMySQL Error : " . $mysqli->error);
      sleep(1);
    }

    return false;

  }







  function addgzfile($infile) {
    global $mysqli;

    $fileId = -1;

    $infile = $mysqli->real_escape_string($infile);

    /* If we have to retrieve large amount of data we use MYSQLI_USE_RESULT */
    if (false !== ($result = $mysqli->query("SELECT * FROM `tracs-processed` WHERE gzfile = '$infile';"))) {

      if($result->num_rows === 1) {
        $file = $result->fetch_assoc();
        $fileId = true;
        $result->close();
      }
      else {
        $result->close();
        $result = $mysqli->query("INSERT INTO `tracs-processed` (id, gzfile) VALUES(NULL, '$infile');");
        if(false === $result) {
          $fileId = false;
          print(date("H:i:s", time()) . ": \nMySQL error : " . $mysqli->error."\n");
          sleep(1);
        }
        else {
          $fileId = $mysqli->insert_id;
          // $result->close();
        }
      }
    }
    else {
      print(date("H:i:s", time()) . ": \nMySQL Error : " . $mysqli->error);
      sleep(1);
    }

    return $fileId;

  }



  function addfile($filename) {
    global $mysqli;

    $fileId = -1;

    $filename = $mysqli->real_escape_string($filename);

    /* If we have to retrieve large amount of data we use MYSQLI_USE_RESULT */
    if (false !== ($result = $mysqli->query("SELECT * FROM `tracs-files` WHERE filename = '$filename';"))) {

      if($result->num_rows === 1) {
        $file = $result->fetch_assoc();
        $fileId = (int) $file['id'];
        $result->close();
      }
      else {
        $result->close();
        $result = $mysqli->query("INSERT INTO `tracs-files` (id, filename) VALUES(NULL, '$filename');");
        if(false === $result) {
          $fileId = false;
          print(date("H:i:s", time()) . ": MySQL error : " . $mysqli->error);
        }
        else {
          $fileId = $mysqli->insert_id;
          // $result->close();
        }
      }
    }
    else {
      print(date("H:i:s", time()) . ": \nMySQL Error : " . $mysqli->error);
      sleep(1);
    }

    return $fileId;

  }



  function addproject($project) {
    global $mysqli;

    $projectId = -1;

    $project = $mysqli->real_escape_string($project);

    /* If we have to retrieve large amount of data we use MYSQLI_USE_RESULT */
    if (false !== ($result = $mysqli->query("SELECT * FROM `tracs-projects` WHERE project = '$project';"))) {

      if($result->num_rows === 1) {
        $project = $result->fetch_assoc();
        $projectId = (int) $project['id'];
        $result->close();
      }
      else {
        $result->close();
        $result = $mysqli->query("INSERT INTO `tracs-projects` (id, project) VALUES(NULL, '$project');");
        if(false === $result) {
          $projectId = false;
          print(date("H:i:s", time()) . ": MySQL error : " . $mysqli->error);
          sleep(1);
        }
        else {
          $projectId = $mysqli->insert_id;
        }
      }
  
    }
    else {
      print(date("H:i:s", time()) . ": \nMySQL Error : " . $mysqli->error);
      sleep(1);
    }

    return $projectId;

  }



  function gzfile_get_contents($filename, $use_include_path = 0) { 
    try {

      $file = gzopen($filename, 'rb', $use_include_path); 
      $data = null;
      if ($file) { 
        $data = [];
        while (!gzeof($file)) { 
          $data[] = gzgets($file, 1024); 
        } 
        gzclose($file); 
      } 
    }
    catch(Exception $e) {
      print(date("H:i:s", time()) . get_class($e) . " : " . $e->getMessage()."\n");
    }

    return $data; 
  } 


  function processdataline($line, $startdate) {
    global $summary, $db;

    if(trim($line) == "") {
      return;
    }

    $token  = " - ";

    $app    = "Unknown application";
    $group  = "Unknown project/grouping";
    $title  = "Untitled";

    $project  = "";
    $file     = "";
    // $app      = "";



    $separatorPos = strpos($line, ":");
    if($separatorPos===false) {
      print(date("H:i:s", time()) . ": skipping : " . $line."\n");
      return;
    }
    $sec = intval(trim(substr( $line, 0, $separatorPos )), 10);
    if(!is_numeric($sec)) {
      // that's not a line from Psmith
      print(date("H:i:s", time()) . " : skipping : " . $line."\n");
      return;
    }

    if($sec===0) $sec = 1;

    $caption  = trim(substr( $line, $separatorPos+1 ));

    $caption = @iconv('UTF-8', "ISO-8859-1//IGNORE", $caption);


    $lineItems = explode(" - ", $caption);

    if(count($lineItems) > 1) {
      $app = array_pop($lineItems);
      if(!isset($summary['summary']['apps'][$app])){
        $summary['summary']['apps'][$app] = array("time" => 0, 'sessions' => 0);
      }
      
      $summary['summary']['apps'][$app]['time'] += $sec;
      $summary['summary']['apps'][$app]['sessions'] +=1;
    }

    if(count($lineItems) > 1) {
      $group = array_pop($lineItems);
      if(!isset($summary['summary']['groups'][$group])){
        $summary['summary']['groups'][$group] = array("time" => 0, 'sessions' => 0);
      }
      $summary['summary']['groups'][$group]['time'] += $sec;
      $summary['summary']['groups'][$group]['sessions'] +=1;
    }
    else{
      $title = array_pop($lineItems);
    }

    if(count($lineItems) == 1) {
      $title = array_pop($lineItems);
    }
    else {
      $group = array_pop($lineItems);
      if(!isset($summary['summary']['groups'][$group])){
        $summary['summary']['groups'][$group] = array("time" => 0, 'sessions' => 0);
      }
      $summary['summary']['groups'][$group]['time'] += $sec;
      $summary['summary']['groups'][$group]['sessions'] +=1;
    }

    while( ($titlePart = array_pop($lineItems)) ) {
      $title = $titlePart . " - " . $title;
    }



    $appPos = strpos($caption, " - Sublime Text 2");

    if($appPos) {
      $app = "Sublime Text 2";
      $caption = substr($caption, 0, $appPos);
      $projPos = strrpos($caption, "(");

      $entry = array();

      if($projPos > 5) {
        $project  = trim(substr($caption, $projPos+1), " \t\n\r\0\x0B()");
        $caption  = trim(substr($caption, 0, $projPos), " \t\n\r\0\x0B()");

        $entry['file_id']     = addfile(trim($caption, " \t\n\r\0\x0B()"));
        $entry['project_id']  = addproject($project);
        $entry['seconds']     = $sec;
        $entry['start']       = $startdate;

        $db[] = $entry;

        $file     = basename(str_replace("\\", "/", trim($caption, " \t\n\r\0\x0B()")));

        if(!isset($summary['SublimeText'][$project])){
          $summary['SublimeText'][$project] = array();
        }
        if(!isset($summary['SublimeText'][$project][$file])){
          $summary['SublimeText'][$project][$file] = array("time" => 0, 'sessions' => 0);
        }
        $summary['SublimeText'][$project][$file]['time'] += $sec;
        $summary['SublimeText'][$project][$file]['sessions']++;

        if(is_null($project) || is_null($caption) || is_null($file)){
          print(date("H:i:s", time()) . " : ONE ORE MORE NULLs : $project $caption $file\n");
          sleep(5);
        }

      }
      else {
        $project  = "none";
        $file     = basename(str_replace("\\", "/", trim($caption, " \t\n\r\0\x0B()")));
        $key      = $caption;


        $entry['file_id']     = addfile(trim($caption, " \t\n\r\0\x0B()"));
        $entry['project_id']  = addproject("none");
        $entry['seconds']     = $sec;
        $entry['start']       = $startdate;

        $db[] = $entry;


        if(is_null($project) || is_null($key) || is_null($file)){
          print(date("H:i:s", time()) . ": ONE ORE MORE NULLs : $project $key $file\n");
          sleep(5);
        }

        if(!isset($summary['SublimeText']['unknown'][$key])){
          $summary['SublimeText']['unknown'][$key] = array("time" => 0, 'sessions' => 0);
        }

        $summary['SublimeText']['unknown'][$key]['time'] += $sec;
        $summary['SublimeText']['unknown'][$key]['sessions'] +=1;
      }


      if(!isset($summary['summary']['files'][$file])){
        $summary['summary']['files'][$file] = array("time" => 0, 'sessions' => 0);
      }
      if(!isset($summary['summary']['projects'][$project])){
        $summary['summary']['projects'][$project] = array("time" => 0, 'sessions' => 0);
      }
      $summary['summary']['files'][$file]['time'] += $sec;
      $summary['summary']['files'][$file]['sessions'] +=1;
      $summary['summary']['projects'][$project]['time'] += $sec;
      $summary['summary']['projects'][$project]['sessions'] +=1;

      // print("$project:$file\ttime: " . $sec . "s;\n");
      // print("app: $app >  project: $project >  time: ".$sec."s >  file: $file\n");
    }
    else { // not Sublime Text
      if(is_null($caption) || $caption == "null") {
        sleep(60);
        $caption = "unknown file";
      }
      if(!$caption) {
        $caption = "untitled";
      }
      if(!isset($summary['unknown'][$caption])){
        $summary['unknown'][$caption] = array("time" => 0, 'sessions' => 0);
      }
      $summary['unknown'][$caption]['time'] += $sec;
      $summary['unknown'][$caption]['sessions']+=1;
    }

    return $sec;  
  }


  function processgzinfile($gzinfile, $startdate) {
    // global $reportend, $reportstart;

    $totaltime  = 0;
    $contents   = gzfile_get_contents($gzinfile);

    if(is_array($contents)) {
      foreach ($contents as $line) {
        $totaltime += processdataline($line, $startdate);
      }
    }
    else {
      throw new PiException("not an array :\n" . json_encode($contents, JSON_PRETTY_PRINT));
    }
    return $totaltime;
  }

  // die(GZIN. "\n");

  print(date("H:i:s", time()) . ": starting...\n\n");

  // do the thing

  $sumtime    = 0;
  $sumfiles   = 0;
  $firstfile  = null;
  $lastfile   = null;

  $startdate  = 0;

  $infiles = array();
  $gzfiles = glob(GZIN . '*.gz');

  foreach ( $gzfiles as $gzfile) {
    if(hasgzfile(basename($gzfile))) {
      continue;
    }
    else{
      $infiles[] = $gzfile;
    }
  }



  $idx = 0;
  $count = count($infiles);

  if($count===0) {
    exit(date("H:i:s", time()) . ": nothing to do ... bye.\n");
  }

  foreach ( $infiles as $gzinfile) {

    $idx++;
    $file = basename($gzinfile, ".log.gz");
    // print("\nprocessing : $file\n");
    // $date = null;

    if(strrpos($file, "-") > -1 ) {
      if( 0 < ($date = (int)(substr($file, strrpos($file, "-")+1)))) {
        $enddate = (int) $date;
        $date = date(DATE_RSS, (int)$date);
      }
      else {
        $date = substr($file, strrpos($file, "-")+1);
      }
    }

    if(strpos($file, "-")>-1) {
      $length = strrpos($file, "-") - strpos($file, "-");
      if( 0 < ($startdate = (int)(substr($file, strpos($file, "-")+1, $length)))) {
        // print("converting : $startdate...");
        // // $startdate = date(DATE_RSS, (int)$startdate);
        // print("$startdate\n");
      }
    }


    print("\rprocessing ($idx/$count) : " . basename($gzinfile, ".log.gz") . (($date) ? " (ending on $date)" : ""));
    if(!$firstfile) { 
      $firstfile = $gzinfile;
      $lastfile = $firstfile;
      $reportstart = $startdate;
      $reportend = $enddate;
    } 
    else { 
      $lastfile = $gzinfile;
      $reportend = $enddate;
    }

    $summary['filelist'][] = $gzinfile;
    // print("calling: processgzinfile('$gzinfile', $startdate);\n");
    $sumtime += processgzinfile($gzinfile, $startdate);
    $sumfiles++;
  }

  $summary['total']['files']  = $sumfiles;
  $summary['total']['time']   = (($d = floor($sumtime/86400)) > 0 ? "$d:" : '') . date("H:i:s", $sumtime % 86400);
  // $summary['total']['time']   = sprintf("%02d%s%02d%s%02d", floor($sumtime/3600), ":", ($sumtime/60)%60, ":", $sumtime%60);
  // $summary['total']['time']   = gmdate("H:i:s", $sumtime);

  $fileparts = explode("-", basename($firstfile, ".log.gz"));
  $summary['total']['start']    = date(DATE_RSS, $fileparts[1]);
  $summary['DEBUG']['start']    = $fileparts[1];


  $fileparts = explode("-", basename($lastfile, ".log.gz"));
  $summary['total']['finish']   = date(DATE_RSS, $fileparts[2]);
  $summary['DEBUG']['stop']    = $fileparts[2];

  $summary['total']['firstfile']  = basename($firstfile);
  $summary['total']['lastfile']   = basename($lastfile);


  $outfile = "tracs-report-{$reportstart}-{$reportend}.json";

  print(date("H:i:s", time()) . ": \nsaving file : $outfile\n");

  file_put_contents($outfile, json_encode($summary));


  print(date("H:i:s", time()) . ": \nsaved report covering period " . date(DATE_RSS, $reportstart) . " to " . date(DATE_RSS, $reportend)."\n");
  

  $db_query = "";
  $db_infile = "";

  foreach ($db as $entry) {
    $db_infile .= "{$entry['seconds']},{$entry['project_id']},{$entry['file_id']},".date('Y-m-d H:i:s', $entry['start'])."\n";
  }

  file_put_contents(basename($outfile, ".json") . ".csv", $db_infile);

  $db_filename = realpath(__DIR__) . "/" . basename($outfile, ".json") . ".csv";

  $db_query = "LOAD DATA INFILE '$db_filename' INTO TABLE `tracs-sublime` 
      FIELDS TERMINATED BY ','
      OPTIONALLY ENCLOSED BY '\"' 
      LINES TERMINATED BY '\n' 
      (seconds,project_id,file_id,start);";


//  print($db_query."\n");

  print("\ninserting data into db...");

  $querystart = microtime(true);

  try {
    if (false === ($result = $mysqli->query($db_query))) {
      throw new DBException($mysqli->errno . " : " . $mysqli->error, 1);
    }
    print(date("H:i:s", time()) . ": finished in " . (microtime(true) - $querystart));
  }
  catch (DBException $e) {
    die(date("H:i:s", time()) . ": " . get_class($e) . " -> " . $e->getMessage() . "\n");
  }

  print(date("H:i:s", time()) . ": \ncsv file imported, result : " . $result);
  print(date("H:i:s", time()) . ": \ncleaning up files...\n");

  foreach ($infiles as $infile) {
    if(addgzfile(basename($infile)) !== true) {
      print(date("H:i:s", time()) . ": \rremoving : " . basename($infile));
    }
  }



  print("Closing db connection...");

  $mysqli->close();

  print("done!\n");

?>
