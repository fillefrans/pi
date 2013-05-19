<?php

  /**
   * Simple script to tail a nohup.out file and publish
   * the output to a redis pubsub channel
   *
   * @author & @copyright Johan Telstad, Kroma <jt@kroma.no>
   * 
   */

  define('CHANNEL', pathinfo(__FILE__, PATHINFO_BASENAME));


  require("/home/kroma/dev/www/pi/srv/php/pi.config.php");



  $nohupfile    = "/home/kroma/dev/www/pi/srv/php/nohup.out";
  $output       = [];
  $returnvalue  = null;

  $debug        = array();
  $reply        = array();


  if(false ===($fp = fopen($nohupfile, "r"))) {
    die("Unable to open file: $nohupfile\n");
  } 


  // connect to Redis
  try {
    if (false === ($redis = connectToRedis(5, PI_DBG))) {
      die("Fatal error: unable to connect to Redis.\n");
    }
  }
  catch(Exception $e) {
    print(get_class($e) . ": " . $e->getMessage() . "\n");
  }



  try {
    // get stored position from previous run
    if(false === ($FILEPOS = $redis->get($nohupfile))) {
      print ("First run: FILEPOS is FALSE. Initializing to 0.");
      $FILEPOS = 0;
    }
  }
  catch(Exception $e) {
      print(get_class($e) . ": " . $e->getMessage() . "\n");
  }

  $startpos = $FILEPOS;

  //we opened the file at the top, because we want to escape early if it doesn't exist

  fseek($fp, $FILEPOS);
  while (!feof($fp)) {
    $line = trim(fgets($fp));
    if($line==="") {
      continue;
    }
    print(CHANNEL . ": $line\n");
    $recipients = $redis->publish( CHANNEL, $line );
  }

  $FILEPOS = ftell($fp);

  fclose($fp);

  // has file grown since last run ?
  if ( ($FILEPOS ^ $startpos) === 0) {
    die();
  }
  // store our file position for next run
  if(false === ($value = $redis->set($nohupfile, $FILEPOS))){
    print("Error: unable to store FILEPOS ($FILEPOS) in redis db no. ". PI_DBG);
  }
  else{
//    print("Success: stored FILEPOS $FILEPOS\n");
  }




  /**
   * supporting functions
   * 
   */


  function connectToRedis($timeout=5, $db = PI_APP){
    global $reply, $debug;
    $redis = new Redis();
    try{ 
  //      if(false===($redis->connect('127.0.0.1', 6379, $timeout))){
      if(false===($redis->connect(REDIS_SOCK))){
        $debug[] = 'Unable to connect to Redis socket: ' . REDIS_SOCK;
        throw new PiException('Unable to connect to Redis socket: ' . REDIS_SOCK, 1);
        return false;
      }
      if (!$redis->select($db)) {
        throw new PiException("Unable to select redis db no. $db", 1);
      }
      return $redis;
    }
    catch(Exception $e){
      $reply['OK']=0;
      $reply['message'] = get_class($e) . ': ' . $e->getMessage();
      $debug[] =  $reply['message'];
      return false;
    }
  }


  // print debug log
  foreach ($debug as $key => $value) {
    print("$key: $value\n");
  }


?>