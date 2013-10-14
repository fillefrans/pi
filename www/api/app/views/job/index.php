<?php

  /**
   *  app.views.webservice.job
   * 
   *  @author Johan Telstad, jt@enfield.no
   *  @version  v0.3, 23.08.2013
   *
   */


  /**
   *  Define some locations and include application files
   *
   */


  // load global config settings for pi
  require_once( __DIR__ .  "/../../../../../srv/php/pi.php");


  function exception_handler(Exception $e) {
    die(json_encode(array('OK'=>0, 'message'=>'Unhandled '. get_class($e) .": ". $e->getMessage())));
  }


  set_exception_handler('exception_handler');


  // global array var to hold the assembled values of this event
  $item = [];



  /**
   *  Initialize
   *
   */

  $redis    = false;
  $request  = json_decode(file_get_contents('php://input'), true);
  $reply    = array('OK'=>0, 'message'=>"Ambiguous result: Script ran to the end without setting a reply.");

  $db = array('host'=>'localhost', 'port'=>3306, 'db'=>'views_externalservices_direktinfo', 'user'=>'views', 'password'=>'1234tsxx');

  // set output type and disallow caching
  header('Content-Type: application/json; charset=utf-8');
  header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header("Expires: Thu, 25 Feb 1971 00:00:00 GMT"); // Snart ørte-og-børti år siden


  if (DEBUG) {
    $starttime  = microtime(true);
    $debug      = array("Starting...");
  }




  /**
   *  Function checkInput
   *
   * @param   $request  array  (by reference)
   * 
   * @return  bool
   *
   * @todo Verify valid phone number? It's already being done later on, but should maybe be moved here
   */

  function checkInput(&$request) {
    //  verify:
    //          1. we have a valid phone number
    //          2. we have a valid API key
    //          3. we have a valid service key
    //          4. ?

    global $reply, $debug;

    $reply['request'] = $request; //json_decode($rawrequest, true); 



    if ((!isset($request['apikey'])) || (trim($request['apikey']==""))) {
      $reply['OK']      = 0;
      $reply['message'] = 'Missing parameter: apikey->'.$request['apikey'];
      $debug[] = 'Missing parameter: apikey';
      return false;
    }
    elseif(false===verifyApiKey($request['apikey'])){
      $reply['message'] = 'Invalid parameter: apikey->'.$request['apikey'];
      $debug[] = 'Invalid parameter: apikey -> '. $request['apikey'];
      return false;
    }

    if ((!isset($request['job'])) || (trim($request['job']==""))) {
      // TODO: verify job, report id
      $reply['OK']      = 0;
      $reply['message'] = 'Missing parameter: job';
      $debug[] = 'Missing parameter: job';
      return false;
    }

    if ((!isset($request['phone'])) || (trim($request['phone']==""))) {
      // TODO: validate phone no.

      $reply['OK']      = 0;
      $reply['message'] = 'Missing parameter: phone';
      $debug[] = 'Missing parameter: phone';
      return false;
    }
    // remove any whitespace or tabs, then remove any leading "+" or zeroes in remaining string
    $request['phone'] = ltrim(str_replace(" ", "", $request['phone']),"+0");

    return true;
  }


  /**
   *  procedure
   */
  function publishJobItem() {
    global $redis, $reply;
    $redis->publish("pi.srv.job.test", $reply);
  }


  /**
   *  procedure 
   *  
   */
  function sendReply() {
    global $reply, $redis, $debug;
  //  publishJobItem();
    quit();
  }


  /**
   *  procedure
   */
  function quit() {
    global $reply, $timers, $starttime, $debug;
    if (DEBUG) {
      $timers['total'] = microtime(true)-$starttime;
      $reply['debug'] = array('responsetime'=>$timers);
      $reply['log'] = $debug;
    }
    die(json_encode($reply));
  }


  /**
   *  Web Service logic
   *
   */


  if (!checkInput($request)) {
    $debug[] = "Invalid input.";
    sendReply();
  }


  include UTILITIES_DIR."views.simpleredis.php";

  if (false===($redis = connectToRedis())) {
    // function will set reply
    $debug[] = "Unable to connect to Redis.";
    sendReply();
  }
  else {
    $debug[] = 'Successfully connected to Redis.';
  }

  // include UTILITIES_DIR."views.functions.php";

  if (false!==($packedNumber = packNumber($request['phone']))) {
    $request['packedNumber']=$packedNumber;
    $debug[] = 'Number is a Norwegian mobile no., packed : ' .$packedNumber;
    // it's a valid Norwegian Mobile phone no.
    if (true===inRedisCache($redis, $packedNumber)) {
      if (DEBUG) {
        $debug[] = 'Number found in cache: ' . $request['phone'];
        $addstart = microtime(true);
      }
      $getstart = microtime(true);
      if(false===($cache_id = getCacheId($packedNumber))){
        $debug[] = 'WARNING: Unable to retrieve cache_id from permanent cache.';
      }
      else{
        $debug[] = 'Retrieved cache_id from permanent cache: ' . $cache_id;
      }
      $timers['getCacheId'] = microtime(true)-$getstart;

      $debug[] = 'Adding to report.';
      if (false===addToReport($request)) {
        $reply['OK'] = 0;
        $reply['message'] = 'Unable to add number to report.';
        $debug[] = 'Unable to add number to report.';
        sendReply();
      }
      $reply['OK'] = 1;
      $reply['message'] = 'Number added to report: '. $request['phone'];
      $debug[] = $reply['message'];

      if (DEBUG) {
        $timers['addToReport'] = microtime(true)-$addstart;
      }
      // We have the number in cache so when we have added it to the report, we are finished
      sendReply();
    }
    else {  // NOT in cache
      $debug[] = 'Number not in cache.';
      if (DEBUG) {
        $addstart = microtime(true);
      }
      require_once UTILITIES_DIR."views.direktinfo.php";
      if(isNorwegianMobileNumber($request['phone'])){
        $request['phone'] = getNorwegianMobileNumber($request['phone']);
      }
      if (false === ($row = encode_result(getInfoFromWebService($request['phone'])))) {
        $reply['OK']=0;
        $reply['message'] = 'Unable to retrieve information from DirektInfo WebService: ' . $request['phone'];
        $debug[] = 'ERROR! Unable to retrieve information from DirektInfo WebService: ' . $request['phone'];
        sendReply();
      }
      else{
        $reply['OK']=1;
        $reply['message'] = 'Information retrieved from DirektInfo WebService: ' . $request['phone'];
        $debug[] = 'Information retrieved from DirektInfo WebService: ' . $request['phone'];
      }
      if (DEBUG) {
        $timers['DirektInfoWebService'] = microtime(true)-$addstart;
        $debug['ws_result'] = $row;
      }
      // TODO: verify that return value from encode_result will be false if something went wrong
      $reply['OK']=1;
      $reply['message'] = 'Retrieved from DirektInfo WebService.';
      
      if (false===(addToRedisCache($redis, $packedNumber))) {
        $debug[] = 'Error adding packed number to Redis cache: '. $packedNumber;
      }
      else{
        $debug[] = 'SUCCESS: Packed number added to Redis fast-cache: '. $packedNumber;
      }
      
      if (false===($cache_id=addToCache($row))) {
        //$reply['ws_response'] = $entry;
        $reply['OK']=0;
        $reply['message'] = 'Unable to add entry to permanent cache: ' . $request['phone'];
        $debug[] = 'ERROR! Unable to add entry to permanent cache.';
        sendReply();
      }
      else {
        $reply['OK']=1;
        $reply['message'] = 'Successfully added to permanent cache with id '.$cache_id;
        $debug[] = 'Added to permanent cache with id: '.$cache_id.".";
      }
      if (false===addToReport($request, $cache_id)) {
        $reply['OK'] = 0;
        $reply['message'] = 'Unable to add number to report.';
        $debug[] = 'Unable to add number to report.';
      }
      else {
        $reply['OK']=1;
        $reply['message'] = 'Successfully added to report, now in permanent cache with id '.$cache_id;
        $debug[] = 'TOTAL SUCCESS! Added to permanent cache AND to report no. '.$request['job'].".";
      }
      sendReply();
    }
  }
  else { // $packedNumber === FALSE
    $debug[] = 'Number is NOT a Norwegian mobile no., not fast-cacheable : ' .$request['phone'];
    if (false===($cache_id=addToCache($row))) {
      //$reply['ws_response'] = $entry;
      $reply['OK']=0;
      $reply['message'] = 'Unable to add entry to permanent cache.';
      $debug[] = 'ERROR! Unable to add entry to permanent cache.';
      sendReply();
    }
    if (false===addToReport($request, $cache_id)) {
      $reply['OK'] = 0;
      $reply['message'] = 'Unable to add number to report.';
      $debug[] = 'Unable to add number to report.';
    }
    else {
      $reply['OK']=1;
      $reply['message'] = 'Successfully added to report, now in permanent cache with id '.$cache_id;
      $debug[] = 'TOTAL SUCCESS! Added to permanent cache AND added to report no. '.$request['job'].".";
    }
    sendReply();
  }


  /**
   *  Finish up, send reply
   *  If we got this far without errors, we should be OK
   */
  $reply['OK'] = 1;
  $reply['message']="Successfully entered into report, number was found in cache.";

  sendReply();


  function verifyApiKey($key = "") {
    if($key==="xdWhKikqUkYk8C7b0n43LTmc"){
      return true;
    }
    elseif(strtolower($key)==="views"){
      return true;
    }
    else{
      return false;
    }
  }



?>
