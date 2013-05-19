<?php

  /**
   * Simple script to tail a nohup.out file and publish
   * the output to a redis pubsub channel
   *
   * @author & @copyright Johan Telstad, Kroma <jt@kroma.no>
   * 
   */

  define('CHANNEL', basename(__FILE__));


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
    if(false === ($FILEPOS = $redis->get(basename(__FILE__)))) {
      print ("FILEPOS is FALSE. Initializing to 0.");
      $FILEPOS = 0;
    }
  }
  catch(Exception $e) {
      print(get_class($e) . ": " . $e->getMessage() . "\n");
  }


  //we opened the file at the top, because we want to escape early if it doesn't exist

  fseek($fp, $FILEPOS);
  while (!feof($fp)) {
    $line = fgets($fp);
    print(CHANNEL . ": $line\n");
    $redis->publish( CHANNEL, $line );
  }

  $FILEPOS = ftell($fp);

  fclose($fp);

  // store our file position for next run
  if(false === $redis->set(CHANNEL, $FILEPOS)){
    print("Error: unable to store FILEPOS ($FILEPOS) in redis db no. ". PI_DBG);
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