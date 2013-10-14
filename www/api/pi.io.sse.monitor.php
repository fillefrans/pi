<?php

  /**
   *  A pass-through script for relaying all messages on a given
   *  Redis channel to a client as Server-Sent Events which 
   *  can be opened from javascript with EventSource
   *
   *  @todo   add access control, check session validity
   *  @param  string address The pi address to listen to
   * 
   */



  error_reporting(-1);

  // a small cheat so we can re-use code here, 
  // even if we don't have a unix socket

  // define('REDIS_SOCK', '127.0.0.1');
  define('REDIS_SOCK', '/var/data/redis/redis.sock');

  $session = session_start();

  header("Content-Type: text/event-stream\n\n");
  header('Access-Control-Allow-Origin: *');
  header('Cache-Control: no-cache'); 


  // disable buffering
  @apache_setenv('no-gzip', 1);
  @ini_set('zlib.output_compression', 0);
  @ini_set('output_buffering', 'Off');
  @ini_set('implicit_flush', 1);

  // allow 5 minutes of inactivity before disconnect 
  @set_time_limit(300);

  // flush buffers
  ob_implicit_flush(1);

  for ( $i = 0, $level = ob_get_level(); $i < $level; $i++ ) {
    ob_end_flush();
  }

  // start output
  ob_start();

  $ID = 0;

  if(!isset($_REQUEST['address'])) {
    sendEvent("error", "No address.");
    die("No address.");
  }

  $channel = $_REQUEST['address'];


  if(!isset($_SESSION['sessionid'])) {
    if(!isset($_REQUEST['sessionid'])) {
      sendEvent("error", "No session.");
      die("No session.");
    }
    else {
      $_SESSION['sessionid'] = $_REQUEST['sessionid'];
    }
  }




  // on message from Redis pubsub on the DEBUG channel

  function onMessage($redis, $channel, $msg, $event="data") {
    global $ID;

    // strip out newlines
    $message  = str_replace("\n", "", $msg);
    $line     = 0;


    $ID++;

    print("event: $event\n");
    print("address: $channel\n");
    print("id: $ID\n");
    print("data: $message\n");
    print("\n\n");

    // wait for 5 more minutes
    set_time_limit(300);

    ob_flush();
    flush();
  }


  function subscribeToChannel($channels) {


    if( false === ($redis = connectToRedis())) {
      return false;
    }

    if (!is_array($channels)) {
      $channels = array($channels);
    }


    try{

      $redis->subscribe($channels, 'onMessage');
      sendEvent("connected", "welcome");
    }
    catch (Exception $e) {
      sendEvent("error", get_class($e) . ": " . json_encode(exceptionToArray($e)));
      $debug[] = get_class($e) . ": " . $e->getMessage();
    }
  }


  function exceptionToArray(&$e) {
    return array( 'message'=> $e->getMessage(), 'code' => $e->getCode(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace());
  }


  function sendEvent ($event, $data) {
    global $ID;

    // strip out newlines
    $message  = str_replace("\n", "", $data);

    $ID++;

    print("event: $event\n");
    print("id: $ID\n");
    print("data: $message\n");
    print("\n\n");

    ob_flush();
    flush();

  }



  function connectToRedis( $timeout = 5 ){

    $redis = new Redis();
    try{ 
      if(false===($redis->connect(REDIS_SOCK))){
      // if(false===($redis->connect('127.0.0.1', 6379, $timeout, $_SESSION['sessionid']))){
        // sendEvent("error", "Unable to connect to Redis on TCP after $timeout seconds.");
        sendEvent("error", "Unable to connect to Redis on " . REDIS_SOCK);
      }
      return $redis;
    }
    catch(RedisException $e){
      sendEvent("error", get_class($e) . ': ' . json_encode(exceptionToArray($e) ));
      return false;
    }
  }





  /**
   *  main()
   *
   */


  if($_SESSION['sessionid']) {
    subscribeToChannel($channel);
  }
  else {
    sendEvent("error", "No session: " . json_encode($_SESSION));
    // die();
  }


  while (true) {
    // wait for messages from Redis
    usleep(10000);
  }


?>