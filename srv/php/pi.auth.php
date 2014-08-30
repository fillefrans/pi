<?

  // activate debugging
  if (!defined('DEBUG')) {
    define('DEBUG', true);
  }


  /**
   *  Pi Auth class
   *
   *  provides basic authentication for channels and addresses
   *  based on app, user, group or object permissions
   *
   *  Loosely based on the unix permissions system
   *
   * @category pi
   * @package auth
   *
   * @copyright 2011-2014 Views AS
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   * 
   */


  require_once("pi.php");



  // Unix permissions format

  // A bit mask created by ORing together zero or more of the following:

  // Octal Mode Number Description
  // 0400  Allows the owner to read
  // 0200  Allows the owner to write
  // 0100  Allows the owner to execute files and search in the directory
  // 0040  Allows group members to read
  // 0020  Allows group members to write
  // 0010  Allows group members to execute files and search in the directory
  // 0004  Allows everyone or the world to read
  // 0002  Allows everyone or the world to write
  // 0001  Allows everyone or the world to execute files and search in the directory
  // 1000  Sets the sticky bit
  // 2000  Sets the setgid bit
  // 4000  Sets the setuid bit
  // First digit in the above mode number is used to set setuid, setgid, or sticky bit. Each remain digit set permission for the owner, group, and world as follows:

  // 4 = r (Read)
  // 2 = w (Write)
  // 1 = x (eXecute)
  // So you end up creating the triplets for your user by adding above digits. For e.g.

  // To represent rwx triplet use 4+2+1=7
  // To represent rw- triplet use 4+2+0=6
  // To represent r-- triplet use 4+0+0=4
  // To represent r-x triplet use 4+0+1=5






  class PiAuth implements JSONSerializable {

    protected   $starttime  = null;
    protected   $redis      = null;
    protected   $pubsub     = null;
    protected   $namespace  = 'pi';



    public function __construct() {
      $this->starttime = microtime(true);
    }


    function jsonSerialize() {
        $auth = array();
        return $auth;
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