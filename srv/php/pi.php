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
  if(!defined('PI_ROOT')){
    define('PI_ROOT', dirname(__FILE__)."/");
    require_once(PI_ROOT."pi.config.php");
  }

  // include utility classes and libraries
  require_once(PI_ROOT."pi.class.exception.php");
  require_once(PI_ROOT."pi.util.php");




  class Pi {


    protected   $starttime  = null;
    protected   $redis      = null;
    protected   $pubsub     = null;
    protected   $channel    = 'pi';

    public function __construct() {
      $this->starttime = microtime(true);
    }


    private function __init() {

      // open a data connection for redis 
      if( false === ($this->redis = $this->connectToRedis())){
        throw new PiException("Unable to connect data client to redis on " . REDIS_SOCK, 1);
      }
  
      // open a separate connection for pubsub
      // from the redis docs:
      // > A client subscribed to one or more channels 
      // > should not issue commands, although it can 
      // > subscribe and unsubscribe to and from 
      // > other channels. The reply of the ...
      if( false === ($this->pubsub = $this->connectToRedis())){
        throw new PiException("Unable to connect pubsub client to redis on " . REDIS_SOCK, 1);
      }
    }


    private function exceptionToArray(&$e) {
      return array(
                   'class'    => get_class($e), 
                   'message'  => $e->getMessage(), 
                   'file'     => $e->getFile(), 
                   'code'     => $e->getCode(), 
                   'file'     => $e->getFile(), 
                   'line'     => $e->getLine(), 
                   'trace'    => $e->getTrace());
    }


    private function handleException(&$e) {
      if(DEBUG) {
        
      }
    }


    protected function connectToRedis( $db = PI_APP, $timeout = 5 ){
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


    protected function publish($channel, $message=false) {

      if($this->pubsub){
        if($message===false) {
          // we were invoked with only one param, so we assume that's a message for default channel
          $message = $channel;
          $channel = $this->channel;
        }

        $this->pubsub->publish($channel, $message);
      }
      else {
        $this->say("We have no redis pubsub object in function publish()\n");
      }
    }



    protected function subscribe($channel, &$callback=false) {

      if($callback===false) {
        // we were invoked without the callback param, which is not right
        throw new PiException("pi->subscribe() was called without the callback parameter.", 1);
        return false;
      }
      if($this->pubsub){
        $this->pubsub->subscribe($channel, $callback);
      }
      else {
        $this->say("We have no redis pubsub object in function publish()\n");
      }
    }



  }


?>