<?php


  require_once("pi.config.php");


  function getFormattedTime($timestamp = false) {
    if($timestamp === false) {
      $timestamp = time();
    }
    return date("H:i:s ", $timestamp);
  }


  function getFormattedDate($timestamp=false) {
    if($timestamp === false) {
      $timestamp = time();
    }
    return date("d.m.Y H:i:s ", $timestamp);
  }


  function addToCache(&$row){
    global $db, $reply, $request, $debug;

    $mysqli = new mysqli( $db['host'], $db['user'], $db['password'], $db['db'] );

    if(mysqli_connect_errno()){
      $reply['OK'] = 0;
      $reply['message'] = mysqli_connect_error();
      return false;
    }

    $fields = implode(",", array_keys($row));
    $values = implode("','", $row);

    if(count($fields)!==count($values)){
      $reply['OK']      = 0;
      $reply['message'] = 'Number of fields and values do not match in addToCache()';
      $debug[]          = 'ERROR! Number of fields and values do not match in addToCache()';
    }


    $query    = "INSERT INTO cache ($fields) 
                  VALUES('$values');";
    $debug[]   = 'Running query: ' . $query;

    if(FALSE===$mysqli->query($query)){
      $reply['OK'] = 0;
      $reply['message'] = $mysqli->error; 
      $debug[] = 'ERROR! Something went wrong when inserting into cache.'; 
      $debug[] = 'MySQL error: ' . $mysqli->error; 
      return false;
    }
    else{
      return $mysqli->insert_id;
    }
    $mysqli->close();
  }

  function getCacheId($idx=null){
    global $db, $reply, $debug, $item;

    if($idx === null){
      return false;
    }

    $mysqli = new mysqli($db['host'],$db['user'],$db['password'],$db['db']);

    if(mysqli_connect_errno()){
      $reply['OK'] = 0;
      $reply['message'] = mysqli_connect_error();
      $debug[] = 'ERROR! '.$reply['message']; 
      return false;
    }


    $query = "SELECT id, type, county, state, age, lifePhase, sex
        FROM cache 
        WHERE idx = SHA1('$idx')
        LIMIT 1;";

    if(FALSE===($sqlresult=$mysqli->query($query))){
      $reply['OK'] = 0;
      $reply['message'] = $mysqli->error; 
      $debug[] = 'ERROR! '. $reply['message']; 
      return false;
    }
    elseif($sqlresult->num_rows===0){
      $debug[] = 'WARNING! Query of permanent cache query returned 0 rows.';
      //return false;
      $cache_id = "NULL";
    }
    else{
      $thisrow  = $sqlresult->fetch_assoc();
      $item = $thisrow;
      $cache_id = $thisrow['id'];
      //$debug[]  = 'Retrieved cache id: '. $cache_id; 
    }
    $mysqli->close();
    return $cache_id;
  }

  function addToReport(&$request, $cache_id=null){
    global $db, $reply, $debug, $redis, $item;

    $mysqli = new mysqli( $db['host'], $db['user'], $db['password'], $db['db'] );

    if(mysqli_connect_errno()){
      $reply['OK'] = 0;
      $reply['message'] = mysqli_connect_error();
      $debug[] = 'ERROR! '.$reply['message']; 
      return false;
    }


    // create a list of our values. The last 4 are optional, so check availability before adding to array
    $values = implode(", ", array('idx'=> 'SHA1('.$request['phone'].')', $request['job'], is_null($cache_id) ? 'NULL' : $cache_id, isset($request['param1']) ? $request['param1'] : "NULL", isset($request['param2']) ? $request['param2'] : "NULL",isset($request['param3']) ? $request['param3'] : "NULL",isset($request['param4']) ? $request['param4'] : "NULL")); 

    $item['job']    = $request['job'];
    $item['param1'] = isset($request['param1']) ? $request['param1'] : null;
    $item['param2'] = isset($request['param2']) ? $request['param2'] : null;
    $item['param3'] = isset($request['param3']) ? $request['param3'] : null;
    $item['param4'] = isset($request['param4']) ? $request['param4'] : null;


    $redis->publish("pi.app.demo.crossfilter.{$request['job']}", json_encode($item));

    $query = "INSERT INTO reportlines (idx, report_id, cache_id, param1, param2, param3, param4) 
              VALUES($values);";

    ///

//    $debug[] = 'Running query :'.$query; 

    if(FALSE===$mysqli->query($query)){
      $reply['OK'] = 0;
      $reply['message'] = $mysqli->error; 
      $debug[] = 'ERROR! '. $reply['message']; 
      return false;
    }
    else{
      $cache_id = $mysqli->insert_id;
      $debug[] = 'Inserted into reportlines with id '. $cache_id; 
      return $cache_id;
    }
    $mysqli->close();
  }


  function isNorwegianMobileNumber($input){
    $regex = "/^((0047)?|(\+47)?|(47)?)(4|9)(\d{7})$/";
    return (1===preg_match($regex, (string)$input));
  }

  function getNorwegianMobileNumber($input){
    return substr($input,-8);
  }

  function isValidPhoneNumber($input){
    // TODO: create regex to test for valid phone number of any kind
    $regex = "/^((0047)?|(\+47)?|(47)?)(4|9)(\d{7})$/";
    return (1===preg_match($regex, (string)$input));
  }


  function packNumber($number){
    // encode Norwegian phone number into a number between 0 and 20 million
    // this is used for our super-fast bit-array Redis-cache
    if(isNorwegianMobileNumber($number)!== true ){
      return false;
     }
    $str = substr($number, -8);
    $firstdigit = $str[0]; 
    switch ($firstdigit) {
      case '4': return intval(substr($str,-7));
        break;
      case '9': $str[0]=1; return intval($str);
        break;
      default:
        return false;
        break;
    }
  }

/**
  *  From php.net, user gorgo
  * 
  * generates a random password, uses base64: 0-9a-zA-Z/+
  * @param int [optional] $length length of password, default 24 (144 Bit)
  * @return string password
  */

  function generateApiKey($length = 24) {
    if(function_exists('openssl_random_pseudo_bytes')) {
     $password = base64_encode(openssl_random_pseudo_bytes($length, $strong));
     if($strong == TRUE){
       return substr($password, 0, $length); //base64 is about 33% longer, so we need to truncate the result
     }
    }

    //fallback to mt_rand if php < 5.3 or no openssl available
    $characters = '0123456789';
    $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz/+'; 
    $charactersLength = strlen($characters)-1;
    $password = '';

    //select some random characters
    for ($i = 0; $i < $length; $i++) {
       $password .= $characters[mt_rand(0, $charactersLength)];
    }        

    return $password;
  }

/**
  *  
  * 
  * generates a file fingerprint based on filedate, filesize and an md5 hash of the contents
  * @param  string 
  * @return array with fingerprint
  */

  function getFileFingerprint($filename) {
    if(!file_exists($filename)){
      return false;
    }
    // Max. 5 MB will be read for MD5 hash.
    $MAX_FILELENGTH = 5000000; 
    $result = array();
    $result['filesize'] = filesize($filename);
    $result['filedate'] = filemtime($filename);
    $result['md5']      = md5(file_get_contents($filename, NULL, NULL, 0, $MAX_FILELENGTH));
    return $result;
  }


?>