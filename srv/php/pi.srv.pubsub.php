<?php

  /**
   *  pi.srv.pubsub
   *
   *  The pubsub server provides messaging services to client applications
   *  over WebSocket. Each session is forked into a separate process,
   *  which is destroyed on disconnect. This is necessary because PHP is 
   *  single-thread and synchronous. Therefore, once we issue a redis->subscribe()
   *  command, the websocket server will no longer accept incoming connections.
   *  So, we have to pcntl_fork() on every incoming connection, and handle everything
   *  there.
   *
   *  
   * 
   *  Each session creates a Redis instance and then acts as a 
   *  redis/websocket bridge, allowing the client to manage subscriptions
   *  in redis. 
   *  
   *
   *  UAC obviously needs to be added here at some point.
   *
   *
   *  @author Johan Telstad <jt@enfield.no>
   *  @copyright Views AS, 2014
   * 
   */







    define('DEBUG', true);

    declare(ticks=1);


    require_once('pi.config.php');
    require_once('pi.exception.php');
    require_once('pi.util.functions.php');

  	require_once('websocket.server.php');




    function handleException($e) {
      print(json_encode(exceptionToArray($e), JSON_PRETTY_PRINT) . "\n");
    }

    function onTime($redis, $address, $message) {
      print($address . " : " . $message . "\n");
    }


    // catch unhandled exceptions
    set_exception_handler('handleException');




    class PiSession {

      private $sessionid    = 0;
      private $user         = null;

      private $state        = "prefork";

      private $connected    = false;

      private $incoming     = 0;
      private $outgoing     = 0;



      public  $lastactivity = 0;


      public function __construct(IWebSocketConnection $user = null) {
        $this->userid       = ($user !== null) ? -1 : $user->getId();
        $this->connected    = ($user !== null);
        $this->lastactivity = time();

      }

    } // end of class PiSession




    /**
     * The pi session server
     *
     * @author 2011-2014 Johan Telstad, jt@enfield.no
     *
     */


    class PiPubSubServer implements IWebSocketServerObserver{

        protected $DEBUG          = DEBUG;
        protected $currentdb      = 0;
        protected $server         = null;
        protected $address        = 'tcp://0.0.0.0:8008';

        public    $redis          = null;
        public    $pubsub         = null;

        private   $incoming       = 0;
        private   $outgoing       = 0;
        private   $lastactivity   = 0;

        public    $user           = null;
        public    $userid         = false;


        private $state        = "prefork";

        private $pid          = false;

        public function __construct(){
          $this->server = new WebSocketServer( $this->address, 'secretkey');
          $this->server->addObserver($this);
          pcntl_signal(SIGCHLD, array($this, "childSignalHandler"));
          }


        public function childSignalHandler($sig, $pid=null) {
          switch ($sig) {
           case SIGCHLD:
             $this->say("SIGCHLD received");
             // pcntl_wait($status, WNOHANG);
          }
        }

        public function handleException($e) {
          print(json_encode(exceptionToArray($e), JSON_PRETTY_PRINT) . "\n");
        }


        protected function connectToRedis( $timeout = 5, $db = PI_APP, $tcp=false ){
          $redis = new Redis();

          try{ 

          // $redis = new Predis\Client("unix://" . REDIS_SOCK);

            if( false ) {
              if(false===($redis->connect('127.0.0.1', 6379, $timeout))){
                $this->say('unable to connect to redis');
                return false;
              }
              else {
                $this->say("success!");
              }

            }
            else {
              if(false===($redis->connect(REDIS_SOCK))){
                $this->say('unable to connect to redis');
                return false;
              }
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


        public function onTime($redis, $address, $message) {
          $this->say("time : " . $message);
        }


        protected function startSession($id) {
          // if( false === ($this->pubsub = $this->connectToRedis())){
          //   throw new PiException("Unable to connect pubsub to redis on tcp", 1);
          //   return false;
          // }
          // $this->userid = $id;
          // $this->subscribe('pi.service.time');
          // $this->subscribe('pi.session');
          // $this->subscribe('pi.session.' . $id);
          // return true;
        }


        public function onConnect(IWebSocketConnection $user){

          $this->pid = 0; // pcntl_fork();

          if($this->pid==-1) {
            $this->state = "royally forked";
            $this->say('Error in fork()');
          }
          elseif ($this->pid > 0) {
            // we are the parent
            // TODO : 
            $this->state = "parent";
            $this->say('Fork successful, waiting for next connection');
            // $killed = pcntl_wait($status, WNOHANG);
            // $this->say("killed $killed child(ren).");
            //  return;
          }
          else {
            $this->user = $user;
            $userid = $user->getId();
            $this->state = "child #" . $userid;
            $this->say("connected.");

            $user->sendMessage(WebSocketMessage::create(json_encode(array( 'event' => 'pi.session.pubsub.start', 'data' => array('message' =>'welcome', 'userid' => $userid) ))));

            // if( false === ($this->pubsub = $this->connectToRedis())){
            //   throw new PiException("Unable to connect pubsub to redis", 1);
            //   return false;
            // }
            $this->userid = $userid;
          }

          usleep(1000);
        }



        // param $message is a basic PHP type, or associative array -> ready for json_encode()
        protected function sendMessage(IWebSocketConnection $user=null, $message=null) {
          if($message === null) {
            throw new PiException("message parameter is NULL in sendMessage()", 1);
            return false;
          }

          if($user === null) {
            throw new PiException("user is NULL in sendMessage()", 1);
            return false;
          }

          $user->sendMessage(WebSocketMessage::create(json_encode($message)));
        }


        protected function reply(IWebSocketMessage $msg = null, IWebSocketConnection $user = null, $message = null) {

          if($msg === null) {
            throw new PiException("msg parameter is NULL in reply()", 1);
          }

          if($message === null) {
            throw new PiException("message parameter is NULL in reply()", 1);
          }

          if($user === null) {
            throw new PiException("user is NULL in reply()", 1);
          }


          // copy original message
          $reply = json_decode($msg->getData(), true);

          // replace data, but keep address and callback params unchanged
          $reply['data'] = $message;

          // echo callback and address parameters back to client
          // along with the new data
          return $user->sendMessage(WebSocketMessage::create(json_encode($message)));
        }


        // handle incoming requests from client
        public function onMessage( IWebSocketConnection $user, IWebSocketMessage $msg ) {

          // assume the worst
          $result   = false;
          $message  = json_decode($msg->getData(), true);

          $this->incoming++;
          $this->lastactivity = time();

          if(!isset($message['command'])){
            // $this->reply($msg, $user, "No command. 'command' should always be lowercase. Message was: ".$message);
            // return;
          }



          $this->say('onMessage() : ' . json_encode($message));

          if(!$this->pubsub) {
            $this->pubsub = $this->connectToRedis();
            $this->pubsub->subscribe(['pi.service.time'], [$this, 'onPubSubMessage']);
          }
          else {
            $this->pubsub->subscribe(['pi.service.time'], [$this, 'onPubSubMessage']);
          }

          // return;
        }




        // no longer in use, was moved to PiSubscription object
        public function onPubSubMessage($redis, $address, $message){

          $packet = WebSocketMessage::create(json_encode(array('event' => $address, 'data' => json_decode($message, true))));

          $result = $this->user->sendMessage($packet);
          $this->outgoing++;
          $this->lastactivity = time();
          $this->say("(user : {$this->user->getId()}, result: $result) onPubSubMessage ($address) : \n" . json_decode($message, true));
          usleep(1000);
        }


        public function subscribe($address) {
          $result = false;

          if(is_array($address)) {
            $this->say("address is an array, needs to be string");
            throw new PiException("address is an array, needs to be string", 1);
            return false;
          }

          if($address == "") {
            $this->say("address is empty string, needs to be non-empty string");
            throw new PiException("address is empty, needs to be non-empty", 1);
            return false;
          }

          if(false === ($result = $this->pubsub->subscribe(array($address), [$this, 'onPubSubMessage']))){
            $this->say("Error subscribing to '$address', result : $result");
            throw new PiException("Error subscribing to '$address'", 1);
          }
          else {
            $this->say("subscribed to: $address, result : $result");
          }

          return $result;

        }


        protected function unsubscribe($address) {
          if(false === ($result = $this->pubsub->unsubscribe([$address]))){
            $this->say("Error unsubscribing from '$address'");
            throw new PiException("Error unsubscribing from '$address'");
            return false;
          }
          return $result;
        }


        public function onDisconnect(IWebSocketConnection $user){

          $this->say("disconnected.");
          $this->quit('client disconnected.');
        }


        public function onAdminMessage(IWebSocketConnection $user, IWebSocketMessage $msg){
            $this->say("admin message received:");
            $this->say($msg->getData());

            $frame = WebSocketFrame::create(WebSocketOpcode::PongFrame);
            $user->sendFrame($frame);
        }


        public function say($msg=''){
            print("[" . $this->state . "] $msg\n");
        }


        private function init() {

          if( false === ($this->redis = $this->connectToRedis())){
            throw new PiException("Unable to connect to redis on " . REDIS_SOCK, 1);
            return false;
          }

          return true;

        }


        public function quit($message) {
          $this->say($message);
          die('client killed: ' . $this->userid . "\n");
        }


        public function run(){

          if($this->init()) {
            $this->say("\n".APP_PLATFORM." [pubsub service] listening on ".$this->address);
            $this->server->run();
          }
          else {
            $this->say("something went wrong, init() returned false.");
          }
        }


    } // class dismissed


  $server = new PiPubSubServer();
  try {
    $server->run();
  }
  catch(Exception $e) {
    print( get_class($e) . ": " . $e->getMessage() );
  }
?>
