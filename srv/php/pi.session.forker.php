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

    // report all errors
    error_reporting(-1);


    /**
    *  Client class for individual sessions
    *
    *  A small shim to keep track of redis subscriptions
    */

    class PiSessionClient {

      protected $incoming = 0;
      protected $outgoing = 0;


      public $user        = null;
      public $userid      = null;
      public $sessionid   = null;
      public $address     = null;
      
      public $subscriptions   = array();

      
      public function __construct($user, $sessionid, $address) {
        $this->user       = $user;
        $this->userid     = $user->getId();
        $this->sessionid  = $sessionid;
        $this->address    = $address; 
        print("New user: " . $address ."\n");

      }

      public function getUser() {
        return $this->user;
      }


      public function onPubSubMessage($redis, $address, $message){
        // relay to client
        $this->user->sendMessage(WebSocketMessage::create(json_encode(array('address' => $address, 'data' => $message))));
      }

      public function onDisconnect(){
        // relay to client
        // $this->user = null;
        print("User disconnected: " . $this->address ."\n");

      }
    }



    class PiSession implements IWebSocketServerObserver{
        protected $DEBUG        = true;
        protected $server       = null;
        protected $redis        = null;
        protected $pubsub       = null;
        protected $currentdb    = PI_APP;
        protected $port         = PI_SESSION_PORT;

        protected $incoming     = 0;
        protected $outgoing     = 0;
        protected $starttime    = null;
        protected $timeout      = 1;
        protected $lastactivity = null;
        protected $pid          = null;
        protected $ticks        = 1;

        // this keeps a list of children in the parent
        protected $clients      = array();
        protected $subscriptions= array();

        private $alarmInterval  = 300;

        public    $userid       = 'pi.srv.session';
        public    $name         = 'pi.srv.session';
        public    $address      = 'pi.srv.session';
        public    $control      = 'ctrl.pi.srv.session';



  
        public function __construct($port=8008){
          $this->starttime  = time();

          $this->port       = $port;
          // gives us an interval-like function
          register_tick_function(array($this,'onTick'));
        }


        protected function __init() {

          if( false === ($this->redis = $this->connectToRedis())){
            throw new PiException("Unable to connect to redis on " . REDIS_SOCK, 1);
          }

          if( false === ($this->pubsub = $this->connectToRedis())){
            throw new PiException("Unable to connect pubsub on " . REDIS_SOCK, 1);
          }

          $this->server     = new WebSocketServer("tcp://0.0.0.0:" . $this->port, 'secretkey');

          $this->server->addObserver($this);
          $this->say("Session handler started, listening on port ".$this->port);

          pcntl_signal(SIGALRM, [$this, "onAlarm"], true);
        }


        protected function exceptionToArray(&$e) {
          return array( 'message'=> $e->getMessage(), 'code' => $e->getCode(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace());
        }


        protected function handleException(&$e) {
            $reply['OK']      = 0;
            $reply['message'] = "Redis exception: ". $e->getMessage();
            $reply['info']    = $this->exceptionToArray($e);

            $this->say($reply['message']);
            $this->say(json_encode($reply['info']));
        }


        protected function publish($address, $message=false) {

          if($this->pubsub){
            if(!$message) {
              // we were invoked with only one param, so assume it's a message for default address
              $message = $address;
              $address = $this->address;
            }
            $this->pubsub->publish($address, $message);
          }
          else {
            $this->say("We have no pubsub object in function publish()\n");
          }
        }


        protected function connectToRedis( $timeout = 5, $db = PI_APP ){
          $redis = new Redis();
          try{ 
            if(false===($redis->connect(REDIS_SOCK))){
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
          $this->ticks++;
        }

        public function onAlarm($signal) {
          $idle_time = (time() - $this->lastactivity);
          $this->say("Caught SIGALRM");
          $this->say("Last activity was " . $idle_time . "seconds ago."); 

          // if($idle_time < WEBSOCKET_TIMEOUT) {
          //   $this->say("Renewing alarm.");
          //   pcntl_alarm($this->alarmInterval);
          // }
          // else {
          //   $this->quit("Timed out, no activity for {$idle_time} seconds.");
          // }
        }




        public function onConnect(IWebSocketConnection $user){

          $userid     = $user->getId();
          $sessionid  = uniqid();
          $this->say("New user: $userid.");

          $client = new PiSessionClient($user, $sessionid, $this->address . ".$sessionid");

          $this->clients[$userid] = $client;

          // $this->clients[$userid] = array(
          //                                 'userid'      => $userid, 
          //                                 'user'        => $user, 
          //                                 'sessionid'   => $sessionid,
          //                                 'address'     => $this->address . ".$sessionid",
          //                                 'sessionName' => str_replace(".", "-", "pi.session." . $sessionid)
          //                                 );

          $response = array(
                            'address' => 'pi.session.connect', 
                            'data' => array('userid' => $userid, 'sessionid' => $sessionid));


          $this->say("Sending connect event: " . json_encode($response));

          $this->send($user, $response);

          // $this->subscribe($client->address, array($this->clients[$userid], 'onPubSubMessage')); 
        }


        protected function send($user, $mixed){
          $user->sendMessage(WebSocketMessage::create(json_encode($mixed)));
        }


        protected function reply($user, $message="", $status = 0, $event='info'){
          $json = json_encode(array('OK'=>$status, 'message'=>$message, "event"=>$event));
          $user->sendMessage(WebSocketMessage::create($json));
        }


        protected function sendData($user, $data=null, $event='data'){
          $json = json_encode(array('data'=>$data, "event"=>$event));
          $user->sendMessage(WebSocketMessage::create($json));
        }


        public function onPubSubMessage($redis, $address, $message){
          // relay messages to client
          // $this->send( array('address' => $address, 'data' => $message));
        }


        protected function unsubscribe($address){
          if(false === ($result = $this->pubsub->unsubscribe($address))){
            throw new PiException("Error unsubscribing from Redis address '$address'", 1);
          }
        }


        protected function subscribe($address, $callback){

          if(!is_array($address)) {
            $address = array($address);
          }

          $this->say("callback : " . print_r($callback, true));

          if(false === ($result = $this->pubsub->subscribe($address, $callback))){
            throw new PiException("Error subscribing to Redis address '$address'.", 1);
          }
        }
 

        // handle incoming requests from client
        public function onMessage(IWebSocketConnection $user, IWebSocketMessage $msg){

          // assume the worst
          $result   = null;
          $message  = json_decode($msg->getData(), true);

          $id = $user->getId();

          $client = $this->clients[$id];


          $this->incoming++;
          $this->lastactivity = time();

          if(!isset($message['command'])){
            // $user->sendMessage( , "No command. 'command' should always be lowercase. Message was: ".$message);
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

              case 'pi':
                break;

              default:
                $this->reply($user, "Unknown command: '{$message['command']}'", 0, "error");
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
              $result = $this->subscribe($message, array($client, 'onPubSubMessage')); 
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
              $this->quit("Client sent 'quit' command.");
              // kind of _have_ to put the break in there, even if we just died.
              break;
            default:
              throw new PiException("Client sent unknown command: '{$message['command']}'", 1);
              break;
          }

          if($result !== null) {
            $response = array('address' => $message['address'], 'callback' => $message['callback'], 'data' => $result);
            $this->say("sending response to \"{$message['address']}\": " . json_encode($result));
            $this->send($user, $response);
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
              $result = $this->redis->lPush($message['address'], json_encode($message['data']));
              $this->say("Data lPushed onto \"{$message['address']}\": " . $result);
              break;

            case 'unshift'  : // alias
            case 'lpop' : 
              $result = $this->redis->lPop($message['address']);
              $this->say("Data lPopped from \"{$message['address']}\": " . $result);
              break;

            case 'pop'  : // alias 
            case 'rpop' : 
              $result = $this->redis->rPop($message['address']);
              $this->say("Data rPopped from \"{$message['address']}\": " . $result);
              break;

            case 'push'  : // alias
            case 'rpush' :
              $result = $this->redis->rPush($message['address'], json_encode($message['data']));
              $this->say("Data rPushed onto \"{$message['address']}\": " . $result);
              break;

            case 'list' : // alias
              $result = $this->redis->lRange($message['address'], 0, -1);
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

          $userid = $user->getId();
          $client = $this->clients[$userid];
          $client->onDisconnect();
          unset($this->clients[$userid]);

        }


        public function onAdminMessage(IWebSocketConnection $user, IWebSocketMessage $msg){
          $this->say("user # " . $user->getId() . ": admin message received.");

          $frame = WebSocketFrame::create(WebSocketOpcode::PongFrame);
          $user->sendFrame($frame);
        }


        public function say($msg="nothing to say"){
          $message =  getFormattedTime() . "  $msg";

          $this->publish("syslog." . $this->address, $message);
          print("$message\r\n");
        }


        public function run(){
          $this->__init();
          // $this->server->debug = true;
          $this->server->run();
          $this->say(get_class($this) . " running");
        }
    }




  $server = new PiSession();

  try {
    $server->run();
  }
  catch(Exception $e) {
    die(get_class($e) . ": " . $e->getMessage());
  }


?>
