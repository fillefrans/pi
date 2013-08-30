<?

    /**
     *  Pi base class
     *
     *  Implements basic functions that all Pi 
     *  classes will need.
     *
     */



    class Pi {


      private $starttime  = microtime(true);
      private $redis      = null;

      public function __construct() {
      }


      private function __init() {
        // this is the place for any code that raises exceptions
        if( false === ($this->redis = $this->connectToRedis())){
          throw new PiException("Unable to connect to redis on " . REDIS_SOCK, 1);
        }

        $this->say("Service started: ". $this->name);

        $this->channel    = 'pi.svc.session.' . $this->id;
      }


      private function exceptionToArray(&$e) {
        return array( 'code' => $e->getCode(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace());
      }


      private function handleException(&$e) {

      }


      protected function connectToRedis( $db = PI_APP, $timeout = 5 ){
        $redis = new Redis();
        try{ 
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