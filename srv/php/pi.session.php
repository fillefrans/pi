<?php

    define('SESSION_START', microtime(true));
    
    session_start();

    if(!defined('PI_ROOT')){
      define('PI_ROOT', '/home/kroma/dev/www/pi/srv/php/');
      require_once PI_ROOT."pi.config.php";
    }
    require_once APP_ROOT."pi.exception.class.php";
    require_once APP_ROOT."pi.util.functions.php";
  	require_once(APP_ROOT."websocket.server.php");

    error_reporting(E_ALL);
    if(!defined('DEBUG')){
      define('DEBUG', true);
    }


    // store some variables for after we fork
    putenv('session_port=8101');
    putenv('session_id=no session');
    putenv('parent_script='.__FILE__);
    putenv('parent_pid='.getmypid());
    putenv('session_guid='.uniqid());


    // ticks HAS to be in the topmost file, since you cannot use declare() in an include file.
    declare(ticks=16);

  

    /**
     * The pi session handler, a WebSocket application server
     *
     * @author Johan Telstad, jt@kroma.no
     *
     */

    class PiSessionHandler implements IWebSocketServerObserver{
        protected $DEBUG      = true;
        protected $server     = null;
        protected $redis      = null;
        protected $incoming   = 0;
        protected $outgoing   = 0;
        protected $starttime  = null;
        protected $timeout    = 1;
        protected $lastactivity  = null;
        protected $myclient   = null;
        protected $port       = null;
        protected $id         = null;
        protected $parent     = null;
        protected $parentpid  = null;
        protected $ticks      = 1;
        protected $guid       = '.empty-guid';
  
        // Pub/Sub
        protected $requests       = array();
        protected $subscriptions  = array();



        public function __construct($port=8100){
          $this->starttime  = time();

          // read back the env vars we set in the parent process before we forked 
          $this->port       = getenv('session_port');
          $this->id         = getenv('session_id');
          $this->guid       = getenv('session_guid');
          $this->parent     = getenv('parent_script');
          $this->parentpid  = getenv('parent_pid');
          $this->server     = new WebSocketServer("tcp://0.0.0.0:".$this->port, 'secretkey');
          $this->server->addObserver($this);
          $this->say("Session handler started, listening on port ".$this->port);
          $this->channel    = 'pi.debug.log.session.' . $this->guid;

          // gives us a timer-function of sorts
          register_tick_function(array($this,'onTick'));
        }

        protected function __init() {
          // this is the place for any code that raises exceptions
          if( false === ($this->redis = $this->connectToRedis())){
            throw new PiException("Unable to connect to redis on " . REDIS_SOCK, 1);
          }
        }


        protected function exceptionToArray(&$e) {
          return array( 'code' => $e->getCode(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace());
        }


        protected function handleException(&$e) {
            $reply['OK']      = 0;
            $reply['message'] = "Redis exception: ". $e->getMessage();
            
            $reply['info']    = $this->exceptionToArray($e);
            $this->say($reply['info']);
            $this->say($reply['message']);

            $debug[]          = print_r($reply['info'],true);
            $debug[]          = $reply['message'];
        }

        protected function publish($channel, $message, $guid="") {

          if($this->redis){
            $this->redis->publish($channel, $message, $guid);
          }
          else {

            $this->say("We have no redis object in function publish()\n");
          }
        }


        protected function connectToRedis( $timeout = 5, $db = PI_APP ){
          global $reply, $debug;
          $redis = new Redis();
          try{ 
      //      if(false===($redis->connect('127.0.0.1', 6379, $timeout))){
            if(false===($redis->connect(REDIS_SOCK))){
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


        public function onTick(){
          $this->say("That's tick # ".$this->ticks++);
        }

        public function onConnect(IWebSocketConnection $user){
          $this->say("{userid:{$user->getId()}, event: \"connect\", message: \"Welcome.\"}");
          $response = "";
          $result   = null;
          $event    = "info";
          $this->myclient = $user;

          // we need an empty array for request param, since we haven't received any requests yet
          $this->reply(array(),$response, $result, 'status');
        }

        protected function reply($request, $message="", $status = 0, $event='info'){
          $json = json_encode(array('OK'=>$status, 'message'=>$message, "event"=>$event, "request"=>$request));
          $this->myclient->sendMessage(WebSocketMessage::create($json));
        }

        protected function sendData($data=null, $event='data'){
          $json = json_encode(array('data'=>$data, "event"=>$event));
          $this->myclient->sendMessage(WebSocketMessage::create($json));
        }


        public function onPubSubMessage($redis, $channel, $message){
          foreach ($this->subscriptions as $value){
            if(false !== fnmatch ( $channel , $value['channel'], FNM_CASEFOLD )){
              // we have a subscriber to this channel
              $message['channel'] = $channel;
              $this->sendData($message, 'publish');
              }
            }
        }

        protected function unsubscribe($channel){
          if(isset($this->subscriptions[$channel])){
            unset($this->subscriptions[$channel]);
          }
          if(false === ($result = $this->redis->unsubscribe($channel))){
            throw new PiException("Error unsubscribing from Redis channel '$channel'.", 1);
          }
        }

        protected function subscribe($channel, $request){
          if(false === ($result = $this->redis->subscribe($channel, array($this, 'onPubSubMessage')))){
            throw new PiException("Error subscribing to Redis channel '$channel'.", 1);
          }
          // add subscription to our internal list
          unset($this->subscriptions[$channel]);
        }


        // handle incoming requests from client
        public function onMessage(IWebSocketConnection $user, IWebSocketMessage $msg){
          $this->incoming++;
          $this->lastactivity = time();
          $message = json_decode($msg->getData(), true);
          if(!isset($message['command'])){
            $this->reply($msg, $user, "No command. Remember that 'command' should always be lowercase. Message was: ".$message);
            return;
          }
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
              $this->publish($message);
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


        public function onDisconnect(IWebSocketConnection $user){
          $this->say("{$user->getId()} disconnected.");
          $this->say("waiting $this->timeout seconds for reconnect.");
        }


        public function onAdminMessage(IWebSocketConnection $user, IWebSocketMessage $msg){
          $this->say("Admin Message received!");

          $frame = WebSocketFrame::create(WebSocketOpcode::PongFrame);
          $user->sendFrame($frame);
        }


        public function say($msg=''){
          $msg_array  = array( 'message' => $msg, 'time' => time() );
          $this->say(json_encode($msg_array, JSON_PRETTY_PRINT). "\n");

          // publish debug info on redis channel
          $this->publish($this->channel, getFormattedTime() . ": " . $msg);
          print(getFormattedTime() . ": $msg\r\n");
        }


        public function run(){
      		$this->say(get_class($this) . ": running\n");
          $this->__init();
          $this->server->run();
        }
    }




  // let's go

  $server = new PiSessionHandler();

  try {
    $server->run();
  }
  catch(Exception $e) {
    $this->say(get_class($e) . ": " . $e->getMessage() . "\n");
  }



?>
