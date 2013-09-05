<?


  // activate debugging everywhere
  if(!defined('DEBUG')){
    define('DEBUG', true);
  }




  /**
   *  Pi base class
   *
   *  provides basic methods.
   *  includes config and code that 
   *  most other Pi classes need.
   *
   *
   * @author 2011-2013 Johan Telstad <jt@enfield.no>
   * 
   */




  // load pi config
  require_once("pi.config.php");

  // include utility classes and libraries
  require_once(PI_ROOT."pi.exception.php");
  require_once(PI_ROOT."pi.util.php");




  class Pi {


    protected   $starttime  = null;
    protected   $redis      = null;
    protected   $pubsub     = null;
    protected   $namespace  = 'pi';

    public function __construct() {
      $this->starttime = microtime(true);
    }


    protected function __init() {

      // open a data connection for redis 
      print("Connecting to redis...");
      if( false === ($this->redis = $this->connectToRedis())){
        throw new PiException("Unable to connect data client to redis on " . REDIS_SOCK, 1);
        print("failed!\n");
        return false;
      }
  
      // open a separate connection for pubsub
      // from the redis docs:
      // > A client subscribed to one or more channels 
      // > should not issue commands, although it can 
      // > subscribe and unsubscribe to and from 
      // > other channels. The reply of the ...
      if( false === ($this->pubsub = $this->connectToRedis())){
        throw new PiException("Unable to connect pubsub client to redis on " . REDIS_SOCK, 1);
        print("failed!\n");
        return false;
      }
      print("success!\n");
      return true;
    }


    public function exceptionToArray(&$e) {
      return array(
                   'class'    => get_class($e),
                   'message'  => $e->getMessage(),
                   'file'     => $e->getFile(),
                   'code'     => $e->getCode(),
                   'file'     => $e->getFile(),
                   'line'     => $e->getLine(),
                   'trace'    => $e->getTrace()
                   );
    }


    private function handleException(&$e) {
      if(DEBUG) {
        $json = json_encode($e, JSON_PRETTY_PRINT) . "\n";
        file_put_contents(basename(__FILE__, '.php') . '.errorlog', $json, FILE_APPEND);
      }
      print("Unhandled exception:");
      print_r(exceptionToArray($e));
    }


    public function connectToRedis( $db = PI_APP, $timeout = 5 ){
      $redis = new Redis();
      try{ 
        // use pconnect to open a persistent connection
        // a persistent connection is not closed by the close() command
        if(false===($redis->pconnect(REDIS_SOCK))){
          $debug[] = 'Unable to connect to Redis';
          return false;
        }
        $redis->select($db);
        return $redis;
      }
      catch(RedisException $e){
        $this->handleException($e);
        return false;
      }
    }


    public function say($msg="nothing to say"){
      $msg_array  = array( 'message' => $msg, 'time' => time() );

      // publish debug info to our own address
      $this->publish($this->address, getFormattedTime() . ": " . $msg);
      print(getFormattedTime() . ": $msg\r\n");
    }


    protected function publish($address, $message=false) {

      if($this->pubsub){
        if($message===false) {
          // we were invoked with only one param, so we assume that's a message for default address
          $message = $address;
          $address = $this->address;
        }

        $this->pubsub->publish($address, $message);
      }
      else {
        $this->say("We have no redis pubsub object in function publish()\n");
      }
    }



    protected function subscribe($address, &$callback=false) {

      if($callback===false) {
        // we were invoked without the callback param, which is not right
        throw new PiException("Pi->subscribe() was called without the callback parameter.", 1);
        return false;
      }
      if($this->pubsub){
        $this->pubsub->subscribe($address, $callback);
      }
      else {
        $this->say("We have no redis pubsub object in function publish()\n");
      }
    }


  }


?>