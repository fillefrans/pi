<?php

  function connectToRedis($timeout=5){
    global $reply, $debug;
    $redis = new Redis();
    try{ 
//      if(false===($redis->connect('127.0.0.1', 6379, $timeout))){
      if(false===($redis->pconnect('127.0.0.1', 6379))){
        $debug[] = 'Unable to connect to Redis';
        return false;
      }
//      $redis->select();
      return $redis;
    }
    catch(RedisException $e){
      $reply['OK']=0;
      $reply['message'] = "Redis exception: ". $e->getMessage();
      $debug[] =  "Redis exception: ". $e->getMessage();
      return false;
    }
  }

  function inRedisCache($redis, $packedNumber=null){
    global $debug, $reply;
    if($packedNumber===null){
      $debug[] = 'packedNumber is NULL, inRedisCache()';
      return false;
    }
    try{  
      //      $redis->connect('127.0.0.1', 6379);
      if(false===($redis->select(REDIS_CACHE))){
        $debug[] = 'Unable to select Redis DB no. '.REDIS_CACHE;
        return false;
      }
      // Check if cache exists. If not, create it
      if($redis->setBit('cache', 20000000, 1)===0){
        $debug[] = 'FIRST RUN. Allocated Redis cache for 20 million numbers.';
      }
      $result = $redis->getBit('cache', (int)$packedNumber); 
      if($result === 1){
        $debug[] = 'Redis cache HIT: '.$packedNumber;
      }
      else{
        $debug[] = 'Redis cache MISS: '.$packedNumber;
      }
      return ($result === 1);
    }
    catch(RedisException $e){
      $reply['OK']=0;
      $reply['message'] = "Redis exception: ". $e->getMessage();
      $debug[] = "Redis exception: ". $e->getMessage();
      return false;
    }
  }


  function addToRedisCache($redis, $packedNumber=null){
    global $debug, $reply;
    if($packedNumber===null){
      $debug[] = 'packedNumber is NULL, addToRedisCache()';
      return false;
    }
    try{  
//      $redis->connect('127.0.0.1', 6379);
       if(false===($redis->select(REDIS_CACHE))){
        $debug[] = 'Unable to select Redis DB no. '.REDIS_CACHE;
        return false;
      }

      $result = $redis->setBit('cache', (int)$packedNumber, 1);
      if($result===0){
        $debug[]= 'Added to cache: '. $packedNumber;
      }
      if($result===1) {
        $debug[]= 'WARNING! Adding number that is already in cache: '. $packedNumber. '. This should never happen.';
      }
      if($result===false){
        $debug[]= 'WARNING! setBit returned FALSE for number: '. $packedNumber. '. This should never happen.';
      }

      if(DEBUG){
        // verify that bit is set
        $result = $redis->getBit('cache', (int)$packedNumber);
        if($result!==1){
          $debug[] = 'WARNING! Number not set even after setBit: '. $packedNumber;
          $debug[] = 'result = ' .var_export($result,true);
        }
      }
    }
    catch(RedisException $e){
      $reply['OK']=0;
      $reply['message'] = 'Redis exception: '. $e->getMessage();
      $debug[] = 'Redis exception: '. $e->getMessage();
      return false;
    }
  }


?>