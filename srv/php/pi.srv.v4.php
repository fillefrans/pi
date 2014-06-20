<?php

    define('DEBUG', true);


    require_once('pi.config.php');
    require_once('pi.exception.php');
    require_once('pi.util.functions.php');

  	require_once('websocket.server.php');


    function handleException($e) {
      print(json_encode(exceptionToArray($e), JSON_PRETTY_PRINT) . "\n");
    }


    // catch unhandled exceptions
    set_exception_handler('handleException');


    class PiSubscription {

      public $subscribers   = array();
      public $address       = false;
      public $active        = false;
      public $redis         = null;


      public function __construct( &$redis = null,  $address = null, IWebSocketConnection $user = null) {
        if($redis === null) {
          throw new PiException("No redis object given to PiSubscription constructor", 1);
        }
        else {
          $this->redis = $redis;
        }
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


      public function removeSubscriber($userid = null) {
        if($userid === null) {
          throw new PiException("null or no argument given to removeSubscriber()", 1);
        }
        else {
          if(isset($this->subscribers[$userid])) {
            unset($this->subscribers[$userid]);
          }
        }
        $this->active = (count($this->subscribers) > 0);
        return count($this->subscribers);
      }


      public function checkPubSubMessages() {
        $removed = true;

        // read list contents
        $list = $this->redis->lRange($this->address, 0, -1);

        $messages = array();
        // AUDIT : can something happen to the list inbetween these two lines of code?

        // clear list
        $this->redis->del($this->address);

        if(is_array($list)) {
          if(0 < ($count = count($list))) {
            for($i = 0; $i < $count; $i++) {
              $messages[] = json_decode($list[$i], true);
            }
            // print('removing ' . count($list) . ' list from ' . $this->address . "\n");
            // $removed = $this->redis->lRem($this->address, 0, -1);
            // print("$removed list removed.\n");
            $this->onPubSubMessages($messages);
          }
        }
        unset($list);
        return $removed;
      }


      public function onPubSubMessages(&$messages) {

        $packet = WebSocketMessage::create(json_encode(array('address' => $this->address, 'data' => $messages)));

        foreach ($this->subscribers as $userid => $user) {
          // print('sending pubsub packet with ' . count($messages) . ' chunks to user ' . $userid . "\n");
          $user->sendMessage($packet);
        }
        if(false===$this->redis->lRem($this->address, 0, count($messages))) {
          throw new PiException("Error removing ".count($messages)." items from message queue '{$this->address}'");
        }
        else {
          // print("removed some items from queue.");
        }
        $this->lastactivity = time();
      }


      public function addSubscriber(IWebSocketConnection $user = null) {

        if($user === null) {
          throw new PiException("null or no argument given to addSubscriber()", 1);
        }
        else {
          $userid = $user->getId();

          if(!isset($this->subscribers[$userid])) {
            print("user $userid subscribed to {$this->address}\n");
            $this->subscribers[$userid] = $user;
          }
          else {
            print("user $userid already subscribed to {$this->address}\n");
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


      public function __construct(IWebSocketConnection $user = null) {
        $this->userid       = ($user !== null) ? -1 : $user->getId();
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


    class PiServer implements IWebSocketServerObserver {

        protected $DEBUG          = DEBUG;
        protected $currentdb      = 0;
        protected $server         = null;
        protected $address        = 'tcp://0.0.0.0:8000';

        public    $sessions       = array();
        public    $subscriptions  = array();

        public    $redis          = null;
        public    $pubsub         = null;
        public    $mysqli         = null;

        protected $ticks          = 0;
        protected $previoustick   = 0;

        private   $incoming       = 0;
        private   $outgoing       = 0;
        private   $lastactivity   = 0;

        protected $running        = false;



        public function __construct() {

          $this->server = new WebSocketServer($this->address, 'secretkey');
          $this->server->addObserver($this);
        }


        public function handleException($e) {

          print(json_encode(exceptionToArray($e), JSON_PRETTY_PRINT) . "\n");
        }


        protected function connectToRedis( $timeout = 5, $db = PI_APP, $tcp=false ) {

          $redis = new Redis();

          try{ 

            if(false) {
              if(false===($redis->pconnect('127.0.0.1', 6379, $timeout))) {
                $this->say('unable to connect to redis on tcp');
                return false;
              }
              else {
                $this->say("success!");
              }

            }
            else {
              if(false===($redis->pconnect(REDIS_SOCK))) {
                $this->say('unable to connect to redis on '.REDIS_SOCK);
                return false;
              }
            }
            
            $redis->select($db);
            return $redis;
          }
          catch(RedisException $e) {
            $this->handleException($e);
            return false;
          }

        }


        public function onTick() {

          $this->ticks++;
          $tick = microtime(true);
          $ticktime = $tick - $this->previoustick;
          $this->previoustick = $tick;

          if( $ticktime < TICK_LENGTH ) {

            // $this->say("short tick : $ticktime, which is " . floor(100* ($ticktime/TICK_LENGTH)) . " % of a regular tick (".TICK_LENGTH.")");
          }
          elseif ( $ticktime > 2*TICK_LENGTH ) {
            $this->say("long tick : $ticktime, which is " . floor(100* ($ticktime/TICK_LENGTH)) . " % of a regular tick (".TICK_LENGTH.")");
          }

          // empties redis message lists, and sends the content to all subscribed clients
          $this->updateSubscriptions();
        }


        public function onConnect(IWebSocketConnection $user) {

          $userid = $user->getId();

          if(isset($this->sessions[$userid])) {
            $this->unsubscribeAll($userid);
            unset($this->sessions[$userid]);
            $this->say('user ' . $userid . ' is already connected: ' . print_r($this->sessions[$userid], true));
            // throw new PiException('user ' . $userid . ' is already connected: ' . print_r($this->sessions[$userid], true), 1);
          }

          $this->sessions[$userid] = new PiSession($user);
          $this->say("{userid: $userid, event: \"connect\", message: \"Welcome.\"}");

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

          return $user->sendMessage(WebSocketMessage::create(json_encode($message)));
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
          // along with the reply
          return $user->sendMessage(WebSocketMessage::create(json_encode($message)));
        }


        // handle incoming requests from client
        // this function is pseudo-private: it has to be public because it's a Redis
        // Pubsub function, but it should still only be called from trusted code
        public function onMessage( IWebSocketConnection $user, IWebSocketMessage $msg ) {

          // assume the worst
          $result   = false;
          $message  = json_decode($msg->getData(), true);

          $this->incoming++;
          $this->lastactivity = time();

          if(!isset($message['command'])) {
            if($message) {
              $this->reply($msg, $user, "No command. 'command' should always be lowercase. Message was: ".$message);
            }
            // because IE is retarded, it sends blank messages every 30 secs
            // else {
            //   $this->say('Skipping empty message from user ' . $user->getId());
            // }

            return;
          }

          // IF command CONTAINS '.' AND IF NOT command STARTS WITH '.'
          // NB! Depends on non-strict comparison
          // do not change if you do not understand
          if(false != ($commandpos = strpos($message['command'], '.'))) {
            // split at first '.' into $handler.$subcommand
            $handler    = substr($message['command'], 0, $commandpos);
            $subcommand = substr($message['command'], $commandpos+1);
            switch ($handler) {

              case 'data' : 
                $message['command'] = $subcommand;
                $result = $this->dataCommand($message, $user);
                $this->say("would send result : " . $result);
                // $this->reply($msg, $user, $result);
                break;

              case 'redis':
                // should be refactored, but for now we simply strip the 'redis.' prefix
                // since we have implemented all the redis commands directly in the 
                // top-level message handler further below
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
                $this->say("Unknown command: '{$message['command']}'");
                $this->say("Original message: " . print_r($message));
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
              // $this->say("calling subscribe('{$message['address']}')...");

              try{ $result = $this->subscribe($message['address'], $user);}
              catch (Exception $e) { '$this->subscribe -> ' . $this->say( get_class($e) . " : " . $e->getMessage()); }
              break;
            case 'unsubscribe':
              $result = $this->unsubscribe($message['address'], $user->getId()); 
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
              if($result !== null) {
                $this->reply($msg, $user, $result);
              }
              else {
                $this->say('redisCommand returned : ' . $result . ", message : " . json_encode($message));
              }
              break;

            case 'readfile': 
              $result = file_get_contents(PI_ROOT . $message['fileaddress'] );
              break;

            case 'quit':
              // die("Client sent 'quit' command. Exiting.\n");
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
          // $this->say("handled redis command \"{$message['command']}\": " . $result);
          $this->onTick();
          return;
        }


        // returns a basic variable type, or associative array -> ready for json_encode()

        private function redisCommand($message) {

          $result = false;

          $this->say("SWITCH : " . $message['command']);

          switch ($message['command']) {

            case 'shift' : // alias
            case 'lpush' :
              $result = $this->redis->lPush($message['address'], json_encode($message['data']));
              $this->say("Data lPushed onto \"{$message['address']}\": " . $result);
              break;

            case 'unshift'  : // alias
            case 'lpop' : 
              $result = json_decode($this->redis->lPop($message['address']), true);
              $this->say("Data lPopped from \"{$message['address']}\": " . $result);
              break;

            case 'pop'  : // alias 
            case 'rpop' : 
              $result = json_decode($this->redis->rPop($message['address']), true);
              $this->say("Data rPopped from \"{$message['address']}\": " . $result);
              break;

            case 'push'  : // alias
            case 'rpush' :
              $result = $this->redis->rPush($message['address'], json_encode($message['data']));
              $this->say("Data rPushed onto \"{$message['address']}\": " . $result);
              break;

            case 'list' : // alias
              $result = $this->redis->lRange($message['address'], 0, -1);
              $this->say("Read list from \"{$message['address']}\": " . count($result));
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

          // $this->say("returning $result from redisCommand()");
          return $result;
        }


        // returns basic type or associative array -> ready for json_encode()

        private function dataCommand($message) {

          $result = false;

          switch ($message['command']) {

            case 'query'  : // alias
            case 'read'   :
            case 'list'   :
            case '.read'  :
              $this->say("publishing : " . json_encode($message));
              $result = $this->pubsub->publish('ctrl.pi.service.query', igbinary_serialize($message));
              break;

            case 'write' : // alias
            case 'set'   :
              $result = $this->redis->set($message['address'], json_encode($message['data']));
              $this->say("Wrote to \"{$message['address']}\": " . print_r($message['data'], true));
              break;

            default :
              $this->say("ERROR: no command in message: " . print_r($message, true));
              break;
          }
        
        return $result;
        }



        protected function publish( $address, $json ) {

          return $this->redis->rPush($address, $json);
        }


        protected function subscribe( $address, $user = null ) {

          $result = false;

          if(is_array($address)) {
            $this->say("address is an array, needs to be string");
            throw new PiException("address is an array, needs to be string", 1);
            return false;
          }

          if($address == "") {
            $this->say("address is empty, needs to be non-empty");
            throw new PiException("address is empty, needs to be non-empty", 1);
            return false;
          }


          if($user!==null) {
            // $this->say("Adding subscriber to '$address'");
            if(isset($this->subscriptions[$address])) {
              // $this->say("calling addSubscriber()");
              $result = $this->subscriptions[$address]->addSubscriber($user);
            }
            else {
              $this->subscriptions[$address] = new PiSubscription($this->pubsub, $address, $user);
              try {
                // $this->say("subscribed to: $address, result : $result");

              }
              catch (Exception $e) {
                $this->say(get_class($e) . " : " . $e->getMessage());
              }
            }
          }

          else {  // if $user === null
            $this->say("no user, adding internally owned subscription: $address");
          }

        }


        protected function unsubscribe( $address, $userid=null ) {

          $subscribercount = -1;
          
          if($userid !== null) {

            if(isset($this->subscriptions[$address])) {

              $subscribercount = $this->subscriptions[$address]->removeSubscriber($userid);
              $this->say("unsubscribed from '$address', remaining subscribers : $subscribercount");
              if($subscribercount === 0) {
                $this->say("removing internal subscription '$address', because we have no remaining subscribers.");
                unset($this->subscriptions[$address]);
              }
            }
          }

          return $subscribercount;
        }


        public function unsubscribeAll($userid) {
          
          foreach ($this->subscriptions as $address => $subscription) {
            $this->unsubscribe($address, $userid);
          }            

        }


        public function updateSubscriptions() {

          if(count($this->subscriptions) === 0) {
            return true;
          }
          foreach ($this->subscriptions as $address => $subscription) {
            $subscription->checkPubSubMessages();
          }
          return true;
        }


        public function onDisconnect(IWebSocketConnection $user) {

            // clear all subscriptions
            $this->unsubscribeAll($user->getId());
            // remove user's session object
            unset($this->sessions[$user->getId()]);
            $this->say("user {$user->getId()} disconnected.");
        }


        public function onAdminMessage(IWebSocketConnection $user, IWebSocketMessage $msg) {

            $this->say("admin message received:");
            $this->say($msg->getData());

            $frame = WebSocketFrame::create(WebSocketOpcode::PongFrame);
            $user->sendFrame($frame);
        }


        public function say($msg='') {

            print("$msg\n");
        }


        private function init() {

          if( false === ($this->redis = $this->connectToRedis())) {
            throw new PiException("Unable to connect to redis on " . REDIS_SOCK, 1);
          }

          if( false === ($this->pubsub = $this->connectToRedis())) {
            throw new PiException("Unable to connect pubsub to redis on " . REDIS_SOCK, 1);
          }

          $this->pubsub->select(PI_DATA);

          return ($this->redis && $this->pubsub);
        }


        public function run() {

          // passthru('clear');

          if($this->init()) {
            $this->say("\n".APP_PLATFORM." ".APP_VERSION." listening on ".$this->address);
            $this->running = true;
            $this->previoustick = microtime(true);
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
