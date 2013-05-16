  <?php


    if(!defined('PI_ROOT')){
      define('PI_ROOT', '/home/kroma/dev/www/pi/srv/php/');
      require_once PI_ROOT."pi.config.php";
    }
    require_once APP_ROOT."pi.exception.class.php";
    require_once UTILITIES_DIR."pi.functions.php";
  	require_once(APP_ROOT."websocket.server.php");

    error_reporting(E_ALL);
    if(!defined('DEBUG')){
      define('DEBUG', true);
    }

    if(DEBUG){
      print("DEBUGGING\n");
      putenv('session_port=8101');
      putenv('session_id=no session');
      putenv('parent_script='.__FILE__);
      putenv('parent_pid='.getmypid());
    }


    // Ticks are needed for the setTimeout and setInterval functions in class Timers.
    // HAS to be in the topmost file, cannot use declare() in an include file.

    declare(ticks=100);

  

    /**
     * The Kroma Views Session handler, WebSocket application server
     *
     * @author Johan Telstad, jt@kroma.no
     *
     */

    class KromaSessionHandler implements IWebSocketServerObserver{
        protected $debug      = true;
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
  
        // Pub/Sub
        protected $requests       = array();
        protected $subscriptions  = array();



        public function __construct($port=8100){
          $this->starttime  = time();
          $this->port       = getenv('session_port');
          $this->id         = getenv('session_id');
          $this->parent     = getenv('parent_script');
          $this->parentpid  = getenv('parent_pid');
          $this->server = new WebSocketServer("tcp://0.0.0.0:".$this->port, 'secretkey');
          $this->server->addObserver($this);
          $this->say("Session handler started, listening on port ".$this->port);
          register_tick_function(array($this,'onTick'));
        }


        protected function connectToRedis($timeout=5){
          global $reply, $debug;
          $redis = new Redis();
          try{ 
      //      if(false===($redis->connect('127.0.0.1', 6379, $timeout))){
            if(false===($redis->pconnect('127.0.0.1', 6379))){
              $debug[] = 'Unable to connect to Redis';
              return false;
            }
      //      $redis->select();
            return $redis;
          }
          catch(RedisException $e){
            $reply['OK']=0;
            $reply['message'] = "Redis exception: ". $e->getMessage();
            $debug[] =  "Redis exception: ". $e->getMessage();
            return false;
          }
        }


        public function onTick(){
          $this->say("That's tick no. ".$this->ticks++);
        }

        public function onConnect(IWebSocketConnection $user){
          $this->say("[REDIS-BRIDGE]{userid:{$user->getId()}, event: \"connect\", message: \"Welcome.\"}");
          $response = "";
          $result   = null;
          $event    = "info";
          $this->myclient = $user;
          $this->lastactivity = time();
          try{
            if(false===$this->connectToRedis()){
              $result   = 0;
              $event    = "error";
              $response = "ERROR: Unable to connect to Redis. Closing connection.";
            }
            else{
              $result   = 1;
              $response = "Connected to Redis.";
            }
          }
          catch(Exception $e){
            $result   = 0;
            $event    = "error";
            $response = get_class($e).": ".$e->getMessage();
          }
          // we need an empty array for request param, since we haven't received any request yet
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
          $this->say("pi.session: {$user->getId()} disconnected.");
          $this->say("pi.session: waiting $this->timeout seconds for reconnect.");
        }


        public function onAdminMessage(IWebSocketConnection $user, IWebSocketMessage $msg){
          $this->say("pi.session: Admin Message received!");

          $frame = WebSocketFrame::create(WebSocketOpcode::PongFrame);
          $user->sendFrame($frame);
        }


        public function say($msg=''){
          echo "$msg\r\n";
        }


        public function run(){
          $this->server->run();
        }
    }


  $server = new KromaSessionHandler();
  $server->run();

?>
