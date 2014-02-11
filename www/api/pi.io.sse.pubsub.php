<?php

/**
 *  A pass-through script for relaying all messages on a given
 *  Redis channel to a client via Server-Sent Events
 *
 *  @param string address The address to listen to
 *
 * 
 */

  ini_set('max_execution_time', 1500); //1500 seconds = 25 minutes

  define('REDIS_SOCK', '/var/data/redis/redis.sock');

  // $session = session_start();

  header("Content-Type: text/event-stream; charset=utf-8");
  header('Cache-Control: no-cache'); 


  // disable buffering
  @apache_setenv('no-gzip', 1);
  @ini_set('zlib.output_compression', 0);
  @ini_set('output_buffering', 'Off');
  @ini_set('implicit_flush', 1);

  // flush buffers
  ob_implicit_flush(1);
  for ( $i = 0, $level = ob_get_level(); $i < $level; $i++ ) {
    ob_end_flush();
  }

  // start output
  ob_start();
  error_reporting(0);

  $ID = 0;

  if(!isset($_REQUEST['address'])) {
    sendEvent("error", "No address.");
    die();
  }

  $channel    = $_REQUEST['address'];

  $sessionid  = '1';
  // $sessionid  = $_REQUEST['token'];

  $redis = null;



  // on message from Redis pubsub

  function onMessage($redis, $chan, $msg, $event="data") {
    global $ID;

    if( strpos($chan, 'ctrl.') === 0 ) {
      sendEvent($event, $msg);
      return;
    }

    // strip out newlines
    $message  = str_replace("\n", "", $msg);
    $line     = 0;


    $ID++;

    print("event: $event\n");
    print("id: $ID\n");
    print("data: $message\n\n");

    ob_flush();
    flush();
  }


  // on message from Redis pubsub on CTRL channel

  function onCtrlMessage($redis, $address, $msg, $event="data") {
    // $packet = json_decode($msg, true);
    sendEvent('data', $msg);
  }



  function init($sessionid) {
    global $redis;

    if( false === ($redis = connectToRedis())) {
      return false;
    }

    try{
      sendEvent("message", "subscribing to 'pi.session.$sessionid'");
      $redis->subscribe(array('pi.session.' . $sessionid, 'ctrl.pi.session.' . $sessionid), 'onMessage');
    }
    catch (Exception $e) {
      sendEvent("error", get_class($e) . ": " . $e->getMessage());
      $debug[] = get_class($e) . ": " . $e->getMessage();
    }
  }



  function subscribe($address) {
    global $redis;

    $redis->subscribe(array($address), 'onMessage');
  }



  function sendEvent ($event='data', $data) {
    global $ID;

    // strip out newlines
    $message  = str_replace("\n", "", $data);

    $ID++;

    print("event: $event\n");
    print("id: $ID\n");
    print("data: $message\n");
    print("\n");

    ob_flush();
    flush();

  }



  function connectToRedis( $timeout = 5 ){

    $redis = new Redis();
    try{ 
      if(false===($redis->pconnect(REDIS_SOCK))){
        sendEvent("error", 'Unable to connect to Redis.');
        return false;
      }
      return $redis;
    }
    catch(RedisException $e){
      sendEvent("error", get_class($e) . ': ' . $e->getMessage() );
      return false;
    }
  }



  /**
   *  main()
   *
   */

try {

  init($sessionid);

  while (true) {
    // wait for messages from Redis
    usleep(10000);
  }

}
catch(Exception $e) {
  sendEvent("error", get_class($e) . ': ' . $e->getMessage() );
} 


?>