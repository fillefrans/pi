<?php

    /**
     * π.service.query
     * 
     * The pi query service, a server that listens for 
     * queries, executes them and publishes the results 
     * as requested
     *
     * This is part of the backbone of the pi server
     *
     * @author Johan Telstad, jt@enfield.no, 2011-2013
     *
     */

    require_once "pi.service.php";
    
    require_once "lib/thread.php";


    class PiServiceQuery extends PiService {

        private   $debug            = false;

        private   $subscribercount  = -1;
        private   $running          = false;
        private   $ticks            = -1;
        private   $ticklength       = 0;

        // address is set from filename
        protected $address          = "";
        protected $name             = "";
        protected $commandaddress   = "";

        private   $mysqli           = null;



        public function __construct() {
          $this->address        = basename(__FILE__, '.php');
          $this->commandaddress = "ctrl." . $this->address;
          $this->name       = $this->address;
        }



        /**
         *
         * π.service.query.tick()
         *
         * Emits a tick event to subscribers at a rate of TICKS_PER_SECOND,
         * as defined in pi.config.php
         *
         * Also emits a time event every second, giving the current server time
         * as a float, where the whole part represents a standard unix timestamp
         *
         */


        protected function tick() {

          // $now = microtime(true);
          $this->ticks++;

        }


        private function timeToNextTick() {

          // round up to nearest whole tick
          $nexttick = ceil(microtime(true) * TICKS_PER_SECOND)/TICKS_PER_SECOND;

          // return difference in microseconds 
          // we call microtime *again* on exit, for accuracy
          return floor( A_COOL_MILLION * ($nexttick - microtime(true)) );
        }


        private function quit($msg="No message. Goodbye.") {

          die("quit : " . $msg);
        }


        public function sendData( $redis, $address, $json ) {
          $this->say('sendData : ' . $json);
        }
        

        public function onMessage($redis, $address, $message) {
          $this->say('onMessage ($address) : ' . $message);
        }


        public function sendReport($redis, $address, $message, $chunksPerMessage=100) {

          $result = false;
          $rowcount = 0;
          $bytecount = 0;
          $rows = array();


          $query = "SELECT * 
              FROM cache 
              INNER JOIN reportlines
              ON cache.id = reportlines.cache_id
              LIMIT 1000000;";

              // WHERE job = 2

          // $this->say("running query: $query"); 

          if(FALSE===($sqlresult=$this->mysqli->query($query))) {
            $this->say('ERROR! '. $reply['message']); 
            return false;
          }
          elseif($sqlresult->num_rows===0) {
            $this->say('WARNING! Query returned 0 rows.');
            $cache_id = "NULL";
            return false;
          }
          else {
            $starttime = microtime(true);
            $redis->select(PI_DATA);
            while($rows[] = $sqlresult->fetch_assoc()) {
              // $this->say('sending json, bytes : ' . strlen(json_encode($row)));
              // $thisrow = array('data' => array('row' => $row));
              // $result = $redis->rPush('pi.data.app.views.job.1', json_encode($thisrow));
              // if($result === false) {
              //   $this->say("redis error : " . $redis->getLastError());
              // }
              // $this->say("result #$rowcount: $result");
              if(++$rowcount % $chunksPerMessage === 0) {
                $result = $redis->rPush('pi.data.app.views.job.2', json_encode($rows));
                if($result === false) {
                  $this->say("redis error : " . $redis->getLastError());
                }
                else {
                  // clear $rows array
                  $rows = array();
                  // $this->say("sent #$rowcount rows of data, new total : $result");
                }
              }
            }

            if($rowcount > 0) {
              $result = $redis->rPush('pi.data.app.views.job.2', json_encode($rows));
              if($result === false) {
                $this->say("redis error : " . $redis->getLastError());
              }
              else {
                // clear $rows array
                // $rows = array();
                // $this->say("sent #$rowcount rows of additional data, new total : $result");
              }
            }

            $stoptime = round((microtime(true) - $starttime) * A_COOL_MILLION);
            // $this->say("sent $result rows from mysql in $stoptime microsecs"); 

            $result = $rowcount;
            $this->say("published $rowcount rows to resultset@pi.data.app.views.job.2 in $stoptime microsecs"); 
          }
          return $result;
        }



        public function onCtrlMessage($redis, $address, $message) {

          $msg = igbinary_unserialize($message);

          $redis = new Redis();
          $redis->connect(REDIS_SOCK);

          $this->say('onCtrlMessage : ' . json_encode($msg));

          if($msg['address'] == "pi.data.app.views.job.2") {
            $result = $this->sendReport($redis, "pi.data.app.views.job.2", $message);
            if($result === false) {
              $this->say("error: sendReport() returned false.");
            }
          }

          $redis->close();
        }


        private function listen() {
          return $this->pubsub->subscribe([$this->commandaddress], [$this, 'onCtrlMessage']);
        }


        private function init() {
          $APP_DB = array('host'=>'localhost', 'port'=>3306, 'db'=>'views_externalservices_direktinfo', 'user'=>'views', 'password'=>'1234tsxx');

          if( false === ($this->mysqli = new mysqli($APP_DB['host'],$APP_DB['user'],$APP_DB['password'],$APP_DB['db']))) {
            throw new PiException("Unable to connect to mysql: " . $APP_DB['user'] . '@' . $APP_DB['host'] . ':' . $APP_DB['port'], 1);
          };

          if(mysqli_connect_errno()) {
            throw new PiException("Unable to connect to mysql (".mysqli_connect_error()."): " . $APP_DB['user'] . '@' . $APP_DB['host'] . ':' . $APP_DB['port'], 1);
            return false;
          }

          $this->pubsub->select(PI_DATA);
          $this->redis->select(PI_APP);

          return true;
        }


        public function run($dbg=false) {
          passthru('clear');
          $this->__init();

          $this->init();

          print("\nRunning : " . basename(__FILE__, '.php') . "\n");

          $result = $this->listen();
          $this->say("finished, result : " . $result);

          // $this->running  = true;

          // while ( $this->running === true ) {
          //   $this->tick();
          //   usleep($this->timeToNextTick());
          // }
        }

    }




  $time = new PiServiceQuery();

  try {
    $time->run();
  }
  catch(Exception $e) {
    print(get_class($e) . ": " . $e->getMessage() . "\n");
  }


?>
