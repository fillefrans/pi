<?php

    /**
     * The pi session handler, a WebSocket application server
     *
     * @author Johan Telstad, jt@viewshq.no, 2011-2014
     *
     */


    // ticks HAS to be declared first thing in the topmost file. 
    // FYI: you cannot use declare() in an include file.
    declare(ticks=16);

    define('SESSION_START', microtime(true));
    define('SESSION_INIT',  getenv('session_init'));

    $copy = false;
    $env  = array();


    require_once("pi.config.php");

    require_once(PHP_ROOT."pi.exception.php");
    require_once(PHP_ROOT."pi.util.functions.php");
  	require_once(PHP_ROOT."websocket.server.php");


    class PiSessionHandler implements IWebSocketServerObserver{
        protected $DEBUG        = true;
        protected $server       = null;
        protected $redis        = null;
        protected $currentdb    = PI_APP;

        protected $incoming     = 0;
        protected $outgoing     = 0;
        protected $starttime    = null;
        protected $timeout      = 1;
        protected $lastactivity = null;
        protected $myclient     = null;
        protected $port         = null;
        protected $id           = null;
        protected $parent       = null;
        protected $parentpid    = null;
        protected $ticks        = 1;

        public    $userid       = 'root';
        public    $name         = 'pi.session';
        public    $address      = 'pi.srv.session';


  
        public function __construct($port=8100){
          $this->starttime  = time();

          // gives us an interval-like function
          register_tick_function(array($this,'onTick'));
        }


        protected function __init() {

          if( false === ($this->redis = $this->connectToRedis())){
            throw new PiException("Unable to connect to redis on " . REDIS_SOCK, 1);
          }

          // read back the env vars we set in the parent process before we forked 
          $this->port       = getenv('session_port');
          $this->id         = getenv('session_id');
          $this->parent     = getenv('parent_script');
          $this->parentpid  = getenv('parent_pid');
          $this->server     = new WebSocketServer("tcp://0.0.0.0" . ( $this->port ? ":" . $this->port : '' ), 'secretkey');

          $this->server->addObserver($this);
          $this->say("Session handler started, listening on port ".$this->port);

          $this->address   .=  '.' . $this->userid;
        }


        protected function exceptionToArray(&$e) {
          return array( 'code' => $e->getCode(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace());
        }


        protected function handleException(&$e) {
            $reply['OK']      = 0;
            $reply['message'] = "Redis exception: ". $e->getMessage();
            $reply['info']    = $this->exceptionToArray($e);

            $this->say($reply['message']);
            $this->say($reply['info']);

            $debug[] = print_r($reply['info'],true);
            $debug[] = $reply['message'];
        }


        protected function publish($address, $message=false) {

          if($this->redis){
            if(!$message) {
              // we were invoked with only one param, so assume it's a message for default address
              $message = $address;
              $address = $this->address;
            }
            $this->redis->publish($address, $message);
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
            $this->currentdb = $db;
            return $redis;
          }
          catch(RedisException $e){
            $this->handleException($e);
            return false;
          }
        }


        public function onTick(){
          // $this->say( floor(1000*(microtime(true)-SESSION_START)) . ": tick # " . $this->ticks );
          $this->ticks++;
        }



        public function onConnect(IWebSocketConnection $user){
          $this->say("{userid:{$user->getId()}, event: \"connect\", message: \"Welcome.\"}");

          $response = array('address' => 'pi.session.connect', 'data' => array('userid' => $user->getId(), 'sessionid' => $this->id));
          $this->myclient = $user;

          $this->say("Sending connect event: " . json_encode($response));
          $this->send($response);
        }


        protected function send($mixed){
          $this->myclient->sendMessage(WebSocketMessage::create(json_encode($mixed)));
        }


        protected function reply($message="", $status = 0, $event='info'){
          $json = json_encode(array('OK'=>$status, 'message'=>$message, "event"=>$event));
          $this->myclient->sendMessage(WebSocketMessage::create($json));
        }


        protected function sendData($data=null, $event='data'){
          $json = json_encode(array('data'=>$data, "event"=>$event));
          $this->myclient->sendMessage(WebSocketMessage::create($json));
        }


        public function onPubSubMessage($redis, $address, $message){
          // relay messages to client
          $this->send(array('address' => $address, 'data' => $message));
        }


        protected function unsubscribe($address){
          if(false === ($result = $this->redis->unsubscribe($address))){
            throw new PiException("Error unsubscribing from Redis address '$address'", 1);
          }
        }


        protected function subscribe($address, $request){
          if(false === ($result = $this->redis->subscribe($address, array($this, 'onPubSubMessage')))){
            throw new PiException("Error subscribing to Redis address '$address'.", 1);
          }
        }


        // handle incoming requests from client
        public function onMessage(IWebSocketConnection $user, IWebSocketMessage $msg){

          // assume the worst
          $result   = null;
          $message  = json_decode($msg->getData(), true);

          $this->incoming++;
          $this->lastactivity = time();

          if(!isset($message['command'])){
            $this->reply($msg, $user, "No command. 'command' should always be lowercase. Message was: ".$message);
            return;
          }

          // IF command CONTAINS '.' AND IF NOT command STARTS WITH '.'
          if(false != ($commandpos = strpos($message['command'], '.'))) {
            // split at first '.' into $handler.$subcommand
            $handler    = substr($message['command'], 0, $commandpos);
            $subcommand = substr($message['command'], $commandpos);
            switch ($handler) {
              case 'redis':
                // should be refactored, for now we simply strip the 'redis.' prefix
                // since we have implemented all the redis commands directly in the 
                // top-level message handler further down
                $message['command'] = $subcommand;
                break;

              case 'task':
              case 'file':
                // rewrite [handler.command] to [command][handler]
                // e.g.: "file.read" -> "readfile"
                // since we have implemented some built-in 
                // top-level message handlers further down
                $message['command'] = $subcommand . $handler;
                break;

              case 'io':
              case 'service':
                $message['handler'] = $handler;
                $message['command'] = $subcommand;
                break;

              default:
                $this->reply($message, "Unknown command: '{$message['command']}'", 0, "error");
                throw new PiException("Client sent unknown command: '{$message['command']}'", 1);
                break;
            }
          }

          switch ($message['command']) {
            case 'query':
              // handle DB/SQL queries here
              $result = $this->query($message);
              break;
            case 'subscribe':
              $result = $this->subscribe($message); 
              break;
            case 'unsubscribe':
              $result = $this->unsubscribe($message); 
              break;
            case 'publish':
              $result = $this->publish($this->address, $message);
              break;
            case 'queue':
              $result = $this->handleQueueRequest($message);
              break;
            case 'read':
            case 'write':
            case 'list':

            case 'setbit':
            case 'getbit':
            case 'set':
            case 'get':
            case 'lpop':
            case 'lpush':
            case 'rpop':
            case 'rpush':
            case 'lpushrpop':
            case 'rpushlpop':
              $result = $this->redisCommand($message);
              break;

            case 'readfile': 
              $result = file_get_contents(PHP_ROOT . $message['fileaddress'] );
              break;

            case 'quit':
              die("Client sent 'quit' command. Exiting.\n");
              // kind of _have_ to put the break in there, even if we just died.
              break;
            default:
              throw new PiException("Client sent unknown command: '{$message['command']}'", 1);
              break;
          }

          if($result !== null) {
            $response = array('address' => $message['address'], 'callback' => $message['callback'], 'data' => $result);
            $this->say("sending response to \"{$message['address']}\": " . json_encode($result));
            $this->send($response);
          }

          // this function returns void
          $this->say("handled redis command \"{$message['command']}\": " . $result);
          return;
        }


        private function redisCommand($message) {
          $result = false;

          switch ($message['command']) {

            case 'shift' : // alias
            case 'lpush' :
              $result = $redis->lPush($message['address'], json_encode($message['data']));
              $this->say("Data lPushed onto \"{$message['address']}\": " . $result);
              break;

            case 'unshift'  : // alias
            case 'lpop' : 
              $result = $redis->lPop($message['address']);
              $this->say("Data lPopped from \"{$message['address']}\": " . $result);
              break;

            case 'pop'  : // alias 
            case 'rpop' : 
              $result = $redis->rPop($message['address']);
              $this->say("Data rPopped from \"{$message['address']}\": " . $result);
              break;

            case 'push'  : // alias
            case 'rpush' :
              $result = $redis->rPush($message['address'], json_encode($message['data']));
              $this->say("Data rPushed onto \"{$message['address']}\": " . $result);
              break;

            case 'list' : // alias
              $result = $redis->lRange($message['address'], 0, -1);
              $this->say("Read list from \"{$message['address']}\": " . $result);
              break;

            case 'read' : // alias
            case 'get'  :
              $result = $this->redis->get($message['address']);
              $this->say("Read from \"{$message['address']}\": " . $result);
              break;

            case 'write': // alias
            case 'set':
              $result = $this->redis->set($message['address'], json_encode($message['data']));
              $this->say("Wrote to \"{$message['address']}\": " . print_r($message['data'], true));
              break;

            default:
              $this->say("ERROR: no command in message: " . print_r($message, true));
              break;
          }

          return $result;
        }


        public function onDisconnect(IWebSocketConnection $user){
          $this->say("User {$user->getId()} disconnected.");
          die("Client disconnected. Exiting.");
        }


        public function onAdminMessage(IWebSocketConnection $user, IWebSocketMessage $msg){
          $this->say("user # " . $this->userid . ": admin message received.");

          $frame = WebSocketFrame::create(WebSocketOpcode::PongFrame);
          $user->sendFrame($frame);
        }


        public function say($msg="nothing to say"){
          $msg_array  = array( 'message' => $msg, 'time' => time() );

          // publish debug info to our own address
          if($this->redis){
            $this->publish($this->address, getFormattedTime() . ": " . $msg);
          }
          print(getFormattedTime() . ": $msg\r\n");
        }


        public function run(){
      		$this->say(get_class($this) . ": running");
          $this->__init();
          $this->server->run();
        }
    }




  $server = new PiSessionHandler();

  try {
    $server->run();
  }
  catch(Exception $e) {
    print(get_class($e) . ": " . $e->getMessage());
  }


?>
