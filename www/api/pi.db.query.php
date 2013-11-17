<?php

    /**
     * pi.db.query
     * 
     * The pi db query API service, a script that receives 
     * queries, executes them and returns the results 
     * as JSON
     *
     * This is part of the pi server API (json web services)
     *
     * @author Johan Telstad, jt@enfield.no, 2011-2014
     *
     */

  session_start();

  // set output type and disallow caching
  header('Content-Type: application/json; charset=utf-8');
  header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header("Expires: Thu, 25 Feb 1971 00:00:00 GMT"); // Snart ørte-og-børti år siden


  error_reporting(E_ALL);

  require_once "pi.api.config.php";

  $request = json_decode(file_get_contents('php://input'), true);


  // collect parameters
  $apikey   = isset($request['apikey'])  ? $request['apikey']   : false;
  $format   = isset($request['format'])  ? $request['format']   : "json";
  $address  = isset($request['address']) ? $request['address']  : false;
  $token    = isset($request['token'])   ? $request['token']    : false;
  $job      = isset($request['job'])     ? $request['job']      : false;


  $reply = array(
              'log'   => array(),
              'items' => array(),
              'info'  => array(
                            'format'  => $format, 
                            'address' => $address, 
                            'job'     => $job,
                            'count'   => 0
                          )
            );


  function hasAccess($address, $apikey) {
    return true;
  }


  function addToCache($address, $data) {
    $redis = new Redis();
    $redis->connect(REDIS_SOCK);
    $redis->select(PI_DB);

    if($redis === false) {
      throw new Exception("Redis error: " . $redis->getLastError());
      return false;
    }

    if(is_array($data)) {
      $newlength = $redis->rPush($address, igbinary_serialize($data));
    }
    else {
      $newlength = $redis->rPush($address, igbinary_serialize([$data]));
    }

    $redis->close();

    return $newlength;
  }




  // function addToCache($address, $data) {
  //   $redis = new Redis();
  //   $redis->connect(REDIS_SOCK);
  //   $redis->select(PI_DB);

  //   if($redis === false) {
  //     throw new Exception("Redis error: " . $redis->getLastError());
  //     return false;
  //   }

  //   if(is_array($data)) {
  //     $redis->multi(Redis::PIPELINE);

  //     for ($i = 0; $i < $rowcount; $i++) {
  //       $redis->rPush($address, json_encode($data[$i]));
  //     }
  //     $newlength = $redis->exec();
  //   }
  //   else {
  //     $newlength = $redis->rPush($address, json_encode($data));
  //   }

  //   $redis->close();

  //   return $newlength;
  // }



  function getFromCache($address) {

    $result = array();

    $redis = new Redis();
    $redis->connect(REDIS_SOCK);
    $redis->select(PI_DB);

    if($redis === false) {
      throw new Exception("Redis error: " . $redis->getLastError());
      return false;
    }

    $rows = $redis->lRange($address, 0, -1);
    $redis->close();

    if($rows && (count($rows) > 0)) {
      $rowcount = count($rows);
      for($i = 0; $i < $rowcount; $i++) {
        $result[] = igbinary_unserialize($rows[$i]);
      }
    }
    else {
      $result = false;
    }

    return $result;

  }


  function getFromDB($address, $offset) {

    $APP_DB = array(
                'host'      => 'localhost', 
                'port'      => 3306, 
                'db'        => 'views_externalservices_direktinfo', 
                'user'      => 'views', 
                'password'  => '1234tsxx'
              );

    $mysqli     = connectDB($APP_DB);
    $result     = false;
    $rowcount   = 0;
    $bytecount  = 0;


    $rows = array();

    if($mysqli === false) {
      return false;
    }


    $query = "SELECT 
          cache.id, cache.zipCode, cache.sex, cache.county, cache.state, 
          reportlines.cache_id, reportlines.param1, reportlines.id as counter
        FROM cache 
        INNER JOIN reportlines
        ON cache.id = reportlines.cache_id
        WHERE reportlines.report_id = 2
        ;";

        // WHERE job = 2
        // WHERE reportlines.job = 2

    if(FALSE === ($sqlresult = $mysqli->query($query))) {
      throw new Exception('ERROR! ' . $reply['message']); 
      return false;
    }
    elseif($sqlresult->num_rows === 0) {
      throw new Exception('WARNING! Query returned 0 rows.');
      $cache_id = "NULL";
      return 0;
    }
    else {

      while($row = $sqlresult->fetch_assoc()) {
        $rows[] = $row;
      }

      if(count($rows) > 0) {
        addToCache($address, $rows);
      }
    }
    return $rows;
  }


  function connectDB($APP_DB) {

    if( false === ($mysqli = new mysqli($APP_DB['host'],$APP_DB['user'],$APP_DB['password'],$APP_DB['db']))) {
      throw new Exception("Unable to connect to mysql: " . $APP_DB['user'] . '@' . $APP_DB['host'] . ':' . $APP_DB['port'], 1);
    };

    if(mysqli_connect_errno()) {
      throw new Exception("Unable to connect to mysql (".mysqli_connect_error()."): " . $APP_DB['user'] . '@' . $APP_DB['host'] . ':' . $APP_DB['port'], 1);
      return false;
    }
    return $mysqli;
  }


  function getLastIndex(&$data) {

  }


  function getDataCount(&$data) {
    
  }



  $reply['items'] = getFromCache($address);

  $datacount = count($reply['items']);

  if(is_array($reply['items']) && count($reply['items']) > 0) {
    $offset = $reply['items'][count($reply['items'])-1]['counter'];
    $reply['log'][] = "Found something in cache at address '$address'";
    $reply['log'][] = "Offset is $offset, items returned: {$datacount}";

  }
  else {
    $reply['log'][] = "Nothing found in cache at address '$address'";
    $reply['items'] = array();
    $offset = 0;
  }


  $dbresult = getFromDB($address, $offset);


  // if any new items from DB
  if(count($dbresult)>0) {
    $counter = count($dbresult);
    // add new items to reply
    for($i = 0; $i < $counter; $i++) {
      $reply['items'][] = $dbresult[$i];
    }
  }

  $reply['info']['count'] = $datacount + $counter;
  $reply['info']['cache_hits'] = $datacount;
  $reply['info']['cache_misses'] = $counter;

  $reply['log'][] = "DB returned {$counter} items which were added to cache";

  print(json_encode($reply));

?>