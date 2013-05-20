<?php


    define('PI_ROOT', '/home/kroma/dev/www/pi/srv/php/');
    require_once PI_ROOT."pi.config.php";
    require_once PI_ROOT."pi.exception.class.php";
    require_once PI_ROOT."pi.util.functions.php";

  	require_once("websocket.server.php");




    class TimeHandler extends WebSocketUriHandler {

      private $user = null;

      public function onMessage(IWebSocketConnection $user, IWebSocketMessage $msg){

          parent::say("[TIME] {$msg->getData()}");
          if($this->user === null){
            $this->user = $user;
          }
          $user->sendMessage(WebSocketMessage::create(json_encode(array("time"=> microtime(true)))));
      }


      public function onAdminMessage(IWebSocketConnection $user, IWebSocketMessage $obj){
          $this->say("[TIME] Admin TEST received!");

          $frame = WebSocketFrame::create(WebSocketOpcode::PongFrame);
          $user->sendFrame($frame);
      }
    }


    class EchoHandler extends WebSocketUriHandler{

      public function say($msg=''){
        echo "$msg \r\n";
      }

      public function onMessage(IWebSocketConnection $user, IWebSocketMessage $msg){
          $this->say("[ECHO] {$msg->getData()}");
          // Echo $msg back to user
          $user->sendMessage($msg);
      }

      public function onAdminMessage(IWebSocketConnection $user, IWebSocketMessage $obj){
          $this->say("[ECHO] Admin TEST received!");

          $frame = WebSocketFrame::create(WebSocketOpcode::PongFrame);
          $user->sendFrame($frame);
      }
    }


    /**
     * The Pi session handler, hands off session requests to worker script,
     * allocates session port incrementally between 8100-8999
     * generates unique session id
     *
     */

    class AppSessionHandler extends WebSocketUriHandler{

      private $myclient             = null;
      private $currentSessionPort   = 8100;
      private $currentSessionId     = 0;
      private $currentSessionStart  = 0;
   
      protected function reply($request, $message="", $status = 0, $event='reply'){
        $json = json_encode(array('OK'=>$status, 'message'=>$message, "event"=>$event, "request"=>$request));
        $this->myclient->sendMessage(WebSocketMessage::create($json));
      }


      protected function fork($script){


        // set up child process
        // store some variables for after we fork
        if($this->currentSessionPort<8100) {
          throw new PiException("No session port({$this->currentSessionPort}), something is wrong.", 1);
        }

        $env["session_port"]  =  $this->currentSessionPort;
        $env["session_init"]  =  $this->currentSessionStart;
        $env['session_id']    =  uniqid();
        $env['parent_script'] = __FILE__;
        $env['parent_pid']    = getmypid();

        $pid    = pcntl_fork();
        $params = array($script);
        // foreach ($env as $value) {
        //   array_push($params, $value);
        // }
        
        if ($pid === -1) {
             return false;
        } else if ($pid) {
             // we are the parent
             $this->say("Parent: started child process with pid ".$pid);
             return $pid;
        } else {
          $this->say("Child: starting with pid ".getmypid().". \nScript: $script");
          pcntl_exec("/usr/bin/php",$params,$env);
          print("exec failed!");
          exit(0);
             // we are the child
        }
      }

      protected function newSession(){
        // next session is +1 
        // kind of dirty, but will do for now
        // we should really keep count in Redis, or something similar
        $this->currentSessionStart = microtime(true);

        $this->currentSessionPort = (8100 + (++$this->currentSessionPort % 900));
 
        try{
          if (!file_exists(SESSION_SCRIPT)){
            throw new PiException("Session handler script does not exist: ". SESSION_SCRIPT, 1);
          }
          if(false === ($result = $this->fork(SESSION_SCRIPT))){
            throw new PiException("Fork failed: " . SESSION_SCRIPT, 1);
          }
        }
        catch(Exception $e){
          print(get_class($e).": ".$e->getMessage());
          //reset session count
          $this->currentSessionPort--;
          return false;
        }
        //return a session port between 8100 and 8999;
        return $this->currentSessionPort;
      }


      protected function sendData($data=null, $event='session'){
        $json = json_encode(array('content'=>$data, "event"=>$event));
        $this->myclient->sendMessage(WebSocketMessage::create($json));
      }


      public function onMessage(IWebSocketConnection $user, IWebSocketMessage $msg){
        if($this->myclient===null){
          $this->myclient = $user;
        }
        $message = json_decode($msg->getData(), true);
        if(!isset($message['command'])){
          // reply($request, $message="", $status = 0, $event='info'){
          $this->reply($message, "No command, expected 'session'. Also, remember that keyword 'command' should always be lowercase", 0, "error");
          print('[SESSION] => '.print_r($msg, true));  
          return;
        }
        switch (strtolower($message['command'])) {
          case 'session':

            $this->currentSessionPort  = $this->newSession();
            $success = ($this->currentSessionPort !== false);
            if(DEBUG) {
              if (!$this->currentSessionPort) {
                print("!this->sessionPort\n");
              }
            }
            print("sessionPort: " . $this->currentSessionPort . "\n");
            $this->sendData(array("OK"=>$success, "sessionPort"=>$this->currentSessionPort, "time"=>time()));
            break;
          default:
            $this->reply($message, "Expecting the 'session' command.");
            break;
        }
      }
    }


    /**
     * The pi application server
     *
     * @author Johan Telstad, jt@kroma.no
     *
     */

    class PiServer implements IWebSocketServerObserver{
        protected $debug    = true;
        protected $server   = null;
        protected $address  = 'tcp://0.0.0.0:8000';
        //private 	$redis 	= null;

        public function __construct(){
            $this->server = new WebSocketServer( $this->address, 'secretkey');
            $this->server->addObserver($this);

            $this->server->addUriHandler("session", new AppSessionHandler());
            $this->server->addUriHandler("time",    new TimeHandler());
            $this->server->addUriHandler("echo",    new EchoHandler());
        }


        public function onConnect(IWebSocketConnection $user){
            $this->say("[SERVER]{userid:{$user->getId()}, event: \"connect\", message: \"Welcome.\"}");
        }


        public function onMessage(IWebSocketConnection $user, IWebSocketMessage $msg){

            $this->say("[SERVER] {$user->getId()} says '{$msg->getData()}'");
        }


        public function onDisconnect(IWebSocketConnection $user){
            $this->say("[SERVER] {$user->getId()} disconnected");
        }


        public function onAdminMessage(IWebSocketConnection $user, IWebSocketMessage $msg){
            $this->say("[SERVER] Admin Message received!");

            $frame = WebSocketFrame::create(WebSocketOpcode::PongFrame);
            $user->sendFrame($frame);
        }


        public function say($msg=''){
            print("$msg\n");
        }


        public function run(){
      		print("running\n");
          try{
            $result = $this->server->run();
            

            print(APP_NAME." ".APP_VERSION."\n");
            $this->say("\trunning on ". APP_PLATFORM);
            $this->say("========================================");
            $this->say("Server Started : " . date('Y-m-d H:i:s'));
            $this->say("Listening on   : " . $this->address);
            $this->say("Server status  : " . $result);
            $this->say("========================================");
          }
          catch(Exception $e){
            print("ERROR: " .get_class($e). " -> " .$e->getMessage());
          }
        }
    }

  $server = new PiServer();
  try {
    $server->run();
  }
  catch(Exception $e) {
    print(get_class($e). " -> " .$e->getMessage());
  }
?>
