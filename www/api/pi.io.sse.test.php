<?php

/**
 *  A pass-through script for relaying all messages on a given
 *  Redis channel to a client via Server-Sent Events
 *
 *  @param string address The address to listen to
 *
 * 
 */

  error_reporting(-1);

  define('REDIS_SOCK', '/var/data/redis/redis.sock');


  $ID = 0;

  $channel = "pi.service.numberstation.randomwalk";




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

  }


  function subscribeToChannel($channel) {

    if( false === ($redis = connectToRedis())) {
      return false;
    }

    try{
      $redis->subscribe([$channel], 'onMessage');
      sendEvent("connected", "welcome");
    }
    catch (Exception $e) {
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


  }



  function connectToRedis( $timeout = 5 ){

    $redis = new Redis();
    try{ 
      if(false===($redis->connect(REDIS_SOCK))){
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


  subscribeToChannel($channel);

  while (true) {
    // wait for messages from Redis
    usleep(10000);
  }


?>