<?


  // activate debugging everywhere
  if(!defined('DEBUG')){
    define('DEBUG', true);
  }




  /**
   *  Pi base class
   *
   *  Implements basic functions and
   *  includes files that most other Pi 
   *  classes will need.
   *
   *
   * @author 2011-2013 Johan Telstad <jt@enfield.no>
   * 
   */



  if(!defined('PI_ROOT')){
    define('PI_ROOT', dirname(__FILE__)."/");
    require_once(PI_ROOT."pi.config.php");
  }

  require_once(PI_ROOT."pi.class.exception.php");
  require_once(PI_ROOT."pi.util.php");







  class Pi {


    protected   $starttime  = microtime(true);
    protected   $redis      = null;
    protected   $channel    = 'pi';

    public function __construct() {
    }


    private function __init() {
      // this is the place for any code that raises exceptions
      if( false === ($this->redis = $this->connectToRedis())){
        throw new PiException("Unable to connect to redis on " . REDIS_SOCK, 1);
      }
      $this->channel    = 'pi';
    }


    private function exceptionToArray(&$e) {
      return array( 'code' => $e->getCode(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace());
    }


    private function handleException(&$e) {

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

      if($this->redis){
        if(!$message) {
          // we were invoked with only one param, so assume it's a message for default channel
          $message = $channel;
          $channel = $this->channel;
        }

        $this->redis->publish($channel, $message);
      }
      else {
        $this->say("We have no redis object in function publish()\n");
      }
    }

  }


?>