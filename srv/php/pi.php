<?

  // activate debugging
  if (!defined('DEBUG')) {
    define('DEBUG', true);
  }


  /**
   *  Pi base class
   *
   *  provides basic methods.
   *  includes config and code that 
   *  most other Pi classes need.
   *
   * @category pi
   * @package core
   *
   * @copyright 2011-2014 Views AS
   *
   * @author 2011-2014 Johan Telstad <jt@viewshq.no>
   * 
   */


  require_once("pi.exception.php");
  require_once("pi.config.php");

  require_once("pi.util.functions.php");



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
      if (false === ($this->redis = $this->connectToRedis())) {
        throw new PiException("Unable to connect data client to redis on " . REDIS_SOCK, 1);
        print("Connection to redis failed!\n");
        return false;
      }
  
      // open a separate connection for pubsub
      // from the redis docs:
      // > A client subscribed to one or more channels 
      // > should not issue commands, although it can 
      // > subscribe and unsubscribe to and from 
      // > other channels. The reply of the ...
      if (false === ($this->pubsub = $this->connectToRedis())) {
        throw new PiException("Unable to connect pubsub client to redis on " . REDIS_SOCK, 1);
        print("Connection to redis failed!\n");
        return false;
      }
      return true;
    }


    public static function isPlural($word = null) {
      if (is_string($word)) {
        $word = strtolower($word);
        if (1 < ($length = (strlen($word)))) {
          if ($word[$length-1] === "s") {
            if ($word[$length-2] === "s") {
              return false;
            }
            elseif ($word[$length-2] === "e") {
              return true;
            }
          }
        }
      }
      elseif (is_int($word)) {
        // get both 1 and -1
        return abs($word) !== 1;
      }
    }

    public static function exceptionToArray(&$e) {
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
      if (DEBUG) {
        $json = json_encode($e, JSON_PRETTY_PRINT) . "\n";
        file_put_contents(basename(__FILE__, '.php') . '.errorlog', $json, FILE_APPEND);
      }
      $this->say("UNHANDLED " . get_class($e) . ": " . json_encode(self::exceptionToArray($e), JSON_PRETTY_PRINT));
    }


    public function connectToRedis($db = PI_APP, $timeout = 5) {
      $redis = new Redis();
      try {
        if (false === ($redis->connect(REDIS_SOCK))) {
          $debug[] = 'Unable to connect to Redis';
          return false;
        }
        $redis->select($db);
        return $redis;
      }
      catch (RedisException $e) {
        $this->handleException($e);
        return false;
      }
    }


    public function say($msg="ping") {
      $msg_array  = array('message' => $msg, 'time' => time());

      // publish debug info to our own address
      $this->publish($this->address, getFormattedTime() . ": " . $msg);
      print(getFormattedTime() . ": $msg\n");
    }



    protected function debug($address, $message = false) {

      if ($this->pubsub) {
        if ($message === false) {
          // we were invoked with only one param, so we assume that's a message for default address
          $message = $address;
          $address = $this->address;
        }

        $this->pubsub->publish('debug|' . $address, $message);
      }
      else {
        $this->say("We have no redis pubsub object in function ".__FUNCTION__."()\n");
      }
    }

    protected function log($address, $message = false) {

      if ($this->pubsub) {
        if ($message === false) {
          // we were invoked with only one param, so we assume that's a message for default address
          $message = $address;
          $address = $this->address;
        }

        $this->pubsub->publish('log|' . $address, $message);
      }
      else {
        $this->say("We have no redis pubsub object in function ".__FUNCTION__."()\n");
      }
    }


    protected function publish($address, $json) {
      if (is_array($json)) {
        $json = json_encode($json);
      }

      return $this->redis->rPush($address, $json);
    }


    // protected function publish($address, $message = false) {

    //   if ($this->pubsub) {
    //     if ($message === false) {
    //       // we were invoked with only one param, so we assume that's a message for default address
    //       $message = $address;
    //       $address = $this->address;
    //     }

    //     $this->pubsub->publish($address, $message);
    //   }
    //   else {
    //     $this->say("We have no redis pubsub object in function ".__FUNCTION__."()\n");
    //   }
    // }


    protected function subscribe($address, $callback = false) {

      if ($callback === false) {
        // we were invoked without the callback param, which is not right
        throw new PiException("Pi->".__FUNCTION__."() was called without the callback parameter.", 1);
        return false;
      }
      if ($this->pubsub) {
        $this->pubsub->subscribe($address, $callback);
      }
      else {
        $this->say("We have no redis pubsub object in function ".__FUNCTION__."()\n");
      }
    }

  }


?>