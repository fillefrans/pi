<?php

    /**
     * The pi task master, a server that listens for tasks
     * and executes them as they are requested
     * 
     *
     * This is part of the backbone of our application server
     * It needs to be extra bullet-proof
     *
     * @author Johan Telstad, jt@enfield.no, 2011-2013
     *
     */


    if(!defined('PI_ROOT')){
      define('PI_ROOT', dirname(__FILE__)."/");
      require_once(PI_ROOT."pi.config.php");
    }

    require_once(PI_ROOT."pi.exception.class.php");
    require_once(PI_ROOT."pi.util.functions.php");


    if(!defined('DEBUG')){
      define('DEBUG', true);
    }


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



    class PiService extends Pi{


      private $name       = "svc";



      public function __construct() {
      }



    }



    class PiTaskService extends PiService {

        // handle incoming requests from client
        public function onMessage(IWebSocketConnection $user, IWebSocketMessage $msg){
          $this->incoming++;
          $this->lastactivity = microtime(true);
          $message = json_decode($msg->getData(), true);
 
          switch ($message['command']) {
            case 'query':
              $this->query($message);
              break;
            case 'subscribe': 
              $this->subscribe($message); 
              break;
            case 'unsubscribe': 
              $this->unsubscribe($message); 
              break;
            case 'publish':
              $this->publish($this->channel, $message);
              break;
            case 'queue':
              $this->handleQueueRequest($message);
              break;
            case 'setbit':
            case 'getbit':
            case 'set':
            case 'get':
            case 'lpop':
            case 'rpop':
              $this->redisCommand($message);
              break;
            case 'quit':
              $this->reply($message, "Goodbye.", 1);
              die("Client sent 'quit' command. Exiting.");
              break;
            default:
              $this->reply($message, "Unknown command: '{$message['command']}'", 0, "error");
              break;
          }
        }

        public function run(){
      		$this->say("\n" . get_class($this) . ": running\n");
          $this->__init();
        }
    }




  $server = new PiTaskService();

  try {
    $server->run();
  }
  catch(Exception $e) {
    $this->say(get_class($e) . ": " . $e->getMessage() . "\n");
  }


?>
