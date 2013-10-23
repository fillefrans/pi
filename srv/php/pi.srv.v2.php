<?php

    define('DEBUG', true);

    require_once('pi.config.php');
    require_once('pi.exception.php');
    require_once('pi.util.functions.php');

  	require_once('websocket.server.php');



    class PiSubscription {

      private $subscribers  = array();
      private $address      = false;
      private $active       = false;


      public function __construct($address = null, &$user = null) {
        if($address === null) {
          throw new PiException("No address given to PiSubscription constructor", 1);
        }
        else {
          $this->address = $address;
        }

        if($user !== null) {
          $this->addSubscriber($user);
        }
        $this->active = (count($this->subscribers) > 0);
      }


      public function removeSubscriber(&$user = null) {
        if($user === null) {
          throw new PiException("null or no argument given to removeSubscriber()", 1);
        }
        else {
          if(isset($this->subscribers[$user->getId()])) {
            unset($this->subscribers[$user->getId()]);
          }
        }
        $this->active = (count($this->subscribers) > 0);
        return count($this->subscribers);
      }


      public function onPubSubMessage() {

      }


      public function addSubscriber(&$user = null) {

        if($user === null) {
          throw new PiException("null or no argument given to addSubscriber()", 1);
        }
        else {
          if(!isset($this->subscribers[$user->getId()])) {
            $this->subscribers[$user->getId()] = $user;
          }
        }
        $this->active = (count($this->subscribers) > 0);
        return count($this->subscribers);
      }

    } // end of class PiSubscription





    class PiSession {

      private $sessionid    = 0;
      private $user         = null;

      private $connected    = false;

      private $incoming     = 0;
      private $outgoing     = 0;

      public  $lastactivity = 0;


      public function __construct(&$user = null) {
        $this->user         = $user;
        $this->userid       = $user->getId();
        $this->connected    = ($user !== null);
        $this->lastactivity = time();
      }

    } // end of class PiSession




    /**
     * The pi application server
     *
     * @author 2011-2014 Johan Telstad, jt@enfield.no
     *
     */


    class PiServer implements IWebSocketServerObserver{

        protected $DEBUG          = DEBUG;
        protected $currentdb      = 0;
        protected $server         = null;
        protected $address        = 'tcp://0.0.0.0:8000';

        protected $sessions       = array();
        protected $subscriptions  = array();

        public    $redis          = null;
        public    $pubsub         = null;

        private   $incoming       = 0;
        private   $outgoing       = 0;
        private   $lastactivity   = 0;




        public function __construct(){
          $this->server = new WebSocketServer( $this->address, 'secretkey');
          $this->server->addObserver($this);
        }


        protected function connectToRedis( $timeout = 5, $db = PI_APP ){
          $redis = new Redis();
          try{ 
            if(false===($redis->connect(REDIS_SOCK))){
              $this->say('unable to connect to redis');
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


        protected function handleException(&$e) {
          $reply['OK']      = 0;
          $reply['message'] = "Exception: ". $e->getMessage();
          
          $reply['info']    = exceptionToArray($e);
          $this->say(json_encode($reply['info']));
        }


        public function onConnect(IWebSocketConnection $user){

          $userid = $user->getId();

          if(isset($this->sessions[$userid])) {
            throw new PiException('user ' . $userid . ' is already connected: ' . print_r($this->sessions[$userid], true), 1);
          }
          else {
            $this->sessions[$userid] = new PiSession($user);
            
            if(!isset($this->subscriptions['pi.session'])) {
              $this->subscriptions['pi.session'] = new PiSubscription('pi.session');
            }
            // subscribe to 
            $this->subscriptions['pi.session']->addSubscriber($user);
            $this->subscriptions['pi.session.' . $userid] = new PiSubscription('pi.session.' . $userid, $user);
            $this->say("{userid: $userid, event: \"connect\", message: \"Welcome.\"}");
          }
        }



        // param $message is a basic PHP type, or associative array -> ready for json_encode()
        protected function sendMessage(IWebSocketConnection $user=null, $message=null) {
          if($message === null) {
            throw new PiException("message parameter is NULL in sendMessage()", 1);
          }

          if($user === null) {
            throw new PiException("user is NULL in sendMessage()", 1);
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
            throw new PiException("user is NULL in sendMessage()", 1);
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
            $this->reply($msg, $user, "No command. 'command' should always be lowercase. Message was: ".$message);
            return;
          }

          // IF command CONTAINS '.' AND IF NOT command STARTS WITH '.'
          // NB! Depends on loose comparison
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
                $result = $this->redisCommand($message, $user);
                return;
                break;

              case 'task':
              case 'file':
                // rewrite [handler.command] to [command][handler]
                // e.g. "file.read" -> "readfile"
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
                // $this->reply($message, "Unknown command: '{$message['command']}'", 0, "error");
                throw new PiException("Client sent unknown command: '{$message['command']}'", 1);
                break;
            }
          }

          switch ($message['command']) {
            case 'query':
              // handle DB/SQL queries here
              $result = $this->query($message, $user);
              break;
            case 'subscribe':
              $result = $this->subscribe($message['address'], $user); 
              break;
            case 'unsubscribe':
              $result = $this->unsubscribe($message['address'], $user); 
              break;
            case 'publish':
              $result = $this->publish($message['address'], $message);
              break;
            case 'queue':
              $result = $this->handleQueueRequest($message, $user);
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
              $this->reply($msg, $user, $result);
              break;

            case 'readfile': 
              $result = file_get_contents(PI_ROOT . $message['fileaddress'] );
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
            $response = $message;
            $response['data'] = $result;
            $this->say("sending response to \"{$message['address']}\": " . json_encode($response));
            $this->sendMessage($user, $response);
          }

          // this function returns void
          $this->say("handled redis command \"{$message['command']}\": " . $result);
          return;
        }



        // returns basic type, or associative array -> ready for json_encode()

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
              $result = json_decode($redis->lPop($message['address']), true);
              $this->say("Data lPopped from \"{$message['address']}\": " . $result);
              break;

            case 'pop'  : // alias 
            case 'rpop' : 
              $result = json_decode($redis->rPop($message['address']), true);
              $this->say("Data rPopped from \"{$message['address']}\": " . $result);
              break;

            case 'push'  : // alias
            case 'rpush' :
              $result = $redis->rPush($message['address'], json_encode($message['data']));
              $this->say("Data rPushed onto \"{$message['address']}\": " . $result);
              break;

            case 'list' : // alias
              $result = json_decode($redis->lRange($message['address'], 0, -1), true);
              $this->say("Read list from \"{$message['address']}\": " . $result);
              break;

            case 'read' : // alias
            case 'get'  :
              $result = json_decode($this->redis->get($message['address']), true);
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



        // no longer in use, was moved to PiSubscription object
        public function onPubSubMessage($redis, $address, $message){

          // $packet = WebSocketMessage::create(json_encode(array('address' => $address, 'data' => json_decode($message, true))));

          // foreach ($this->subscriptions[$address]->subscribers as $userid => $user) {
          //   $user->sendMessage($packet); 
          //   $this->outgoing++;
          // }
          $this->say("Pi Server / onPubSubMessage ($address): " . $message);
          $this->lastactivity = time();
        }


        protected function subscribe( $address, $user = null ) {
          $result = false;


          if($user!==null) {
            if(isset($this->subscriptions[$address])) {
              $result = $this->subscriptions[$address]->addSubscriber($user);
            }
            else {
              $this->subscriptions[$address] = new PiSubscription($address, $user);
              if(false === ($result = $this->pubsub->subscribe([$address], [$this->subscriptions[$address], 'onPubSubMessage']))){
                throw new PiException("Error subscribing to '$address'", 1);
              }
              else {
                $this->say("subscribed to: $address");
              }
            }
          }

          else {  // if $user === null
            $this->say("no user, adding internally owned subscription: $address");
          }

        }


        protected function unsubscribe( $address, $user = null ) {
          $subscribercount = -1;
          
          if($user !== null) {

            if(isset($this->subscriptions[$address])) {

              $subscribercount = $this->subscriptions[$address]->removeSubscriber($user);
              if($subscribercount === 0) {
                unset($this->subscriptions[$address]);
              }
            }
          }

          // make sure we have an array as param for redis's unsubscribe
          if(!is_array($address)) {
            $addresses = array($address);
          }
          else {
            $addresses = $address;
          }

          // no active subscribers, so cancel redis subscription
          if ($subscribercount <= 0) {
            if(false === ($result = $this->pubsub->unsubscribe($addresses))){
              throw new PiException("Error unsubscribing from '$address'", 1);
            }
          }
          return $result;
        }


        public function unsubscribeAll(IWebSocketConnection $user){
          
          foreach ($this->subscriptions as $address => $subscription) {
            $this->unsubscribe($address, $user);
            // $subscription->removeSubscriber($user);
          }            

        }


        public function onDisconnect(IWebSocketConnection $user){
            $this->say("user {$user->getId()} disconnected.");
            // clear all subscriptions
            $this->unsubscribeAll($user);
            // remove session object
            unset($this->sessions[$user->getId()]);
        }


        public function onAdminMessage(IWebSocketConnection $user, IWebSocketMessage $msg){
            $this->say("admin message received:");
            $this->say($msg->getData());

            $frame = WebSocketFrame::create(WebSocketOpcode::PongFrame);
            $user->sendFrame($frame);
        }


        public function say($msg=''){
            print("$msg\n");
        }


        private function init() {
          $result = false;

          if( false === ($this->redis = $this->connectToRedis())){
            throw new PiException("Unable to connect to redis on " . REDIS_SOCK, 1);
          }
          if( false === ($this->pubsub = $this->connectToRedis())){
            throw new PiException("Unable to connect pubsub to redis on " . REDIS_SOCK, 1);
          }

          // create a subscription object for pi.session
          $this->subscriptions['pi.session'] = new PiSubscription('pi.session');

          $result = ($this->redis && $this->pubsub);
          return $result;
        }


        public function run(){

          if($this->init()) {
            $this->say("\n".APP_PLATFORM." ".APP_VERSION." listening on ".$this->address);
            $this->server->run();
          }
          else {
            $this->say("something went wrong, init() returned false.");
          }
        }


    } // class dismissed


  $server = new PiServer();
  try {
    $server->run();
  }
  catch(Exception $e) {
    print( get_class($e) . ": " . $e->getMessage() );
  }
?>
