<?php

/**
 * Simple script to tail a nohup.out file and publish
 * the output to a redis pubsub channel
 *
 * @author & @copyright Johan Telstad, Kroma <jt@kroma.no>
 * 
 */

define('CHANNEL', 'pi.debug.log.srv.session');


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
  if (false != ($redis = connectToRedis())) {
    die("Fatal error: unable to connect to Redis.\n");
  }
  catch(Exception $e) {
    print(get_class($e) . ": " . $e->getMessage() . "\n");
  }
}



// connect to Redis
if (false != ($redis = connectToRedis())) {
  try {
    foreach ($debug as $key => $value) {
      $redis->publish( CHANNEL, $value );
    }
  }
  catch(Exception $e) {
    print(get_class($e) . ": " . $e->getMessage() . "\n");
  }
  die('Fatal error: unable to connect to Redis.' . "\n");
}






/**
 * supporting functions
 * 
 */


function connectToRedis($timeout=5){
  global $reply, $debug;
  $redis = new Redis();
  try{ 
//      if(false===($redis->connect('127.0.0.1', 6379, $timeout))){
    if(false===($redis->pconnect('127.0.0.1', 6379))){
      $debug[] = 'Unable to connect to Redis';
      return false;
    }
    $redis->select();
    return $redis;
  }
  catch(RedisException $e){
    $reply['OK']=0;
    $reply['message'] = "RedisException: ". $e->getMessage();
    $debug[] =  "RedisException: ". $e->getMessage();
    return false;
  }
}

?>