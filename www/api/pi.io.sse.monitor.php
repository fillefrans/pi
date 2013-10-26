<?php

/**
 *  A pass-through script for relaying all messages on a given
 *  Redis channel to a client via Server-Sent Events
 *
 *  @param string address The address to listen to
 *
 * 
 */


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

  $channel = $_REQUEST['address'];




  // on message from Redis pubsub on the DEBUG channel

  function onMessage($redis, $chan, $msg, $event="data") {
    global $ID;

    // strip out newlines
    $message  = str_replace("\n", "", $msg);
    $line     = 0;


    $ID++;

    print("event: $event\n");
    print("id: $ID\n");
    print("data: $message\n");
    print("\n");

    ob_flush();
    flush();
  }


  function subscribeToChannel($channel) {

    if( false === ($redis = connectToRedis())) {
      return false;
    }

    try{
      $redis->subscribe(array($channel), 'onMessage');
      sendEvent("message", "subscribed to '$channel'");
    }
    catch (Exception $e) {
      sendEvent("error", get_class($e) . ": " . $e->getMessage());
      $debug[] = get_class($e) . ": " . $e->getMessage();
    }
  }



  function sendEvent ($event, $data) {
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

  subscribeToChannel($channel);

  while (true) {
    // wait for messages from Redis
    usleep(100000);
  }

}
catch(Exception $e) {
  sendEvent("error", get_class($e) . ': ' . $e->getMessage() );
} 


?>