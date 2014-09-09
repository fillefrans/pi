<?php

    /**
     * pi.service.numberstation
     * 
     * The pi numberstation service, a server that broadcasts  
     * semi-random number series and data sets/streams
     * for testing purposes.
     *
     * 
     * 
     * This is part of the pi server toolset
     *
     * @author Johan Telstad, jt@viewshq.no, 2011-2014
     *
     */


    require_once("pi.service.php");


    class PiServiceNumberstation extends PiService {

        private   $debug            = false;

        private   $running          = false;
        private   $ticks            = -1;
        private   $ticklength       = 0;

        protected $address          = "";
        protected $name             = "";

        // private variables for the randomWalk() function
        private $randwalk_position  = 0;
        private $randwalk_delta     = 24;
        private $randwalk_range     = 5000;




        public function __construct() {
          $this->address    = basename(__FILE__, '.php');
          $this->name       = $this->address;
        }




        private function quit($msg="Goodbye. No message.") {
          die($msg);
        }


        protected function randomWalk() {
          $this->randwalk_position += rand(-$this->randwalk_delta, $this->randwalk_delta);

          $this->pubsub->publish("pi.service.numberstation.randomwalk", $this->randwalk_position);
        }

        protected function randomVote() {
          $packet = array();

          $packet['county'] = rand(21);
          $packet['vote'] = rand(9);
          
          $this->pubsub->publish("pi.service.numberstation.randomvote", json_encode($packet));
        }

        protected function tick() {
          // runs at system-configured intervals (TICKS_PER_SECOND)
          // default is 10 times per second

          $this->pubsub->publish("ping.pi.service.numberstation", $this->ticks++);

          $this->randomWalk();

        }


        private function timeToNextTick() {

          // round up to nearest whole tick
          $nexttick = ceil(microtime(true) * TICKS_PER_SECOND)/TICKS_PER_SECOND;

          // return difference in microseconds 
          // we call microtime *again* on exit, for accuracy
          return floor( A_COOL_MILLION * ($nexttick - microtime(true)) );
        }


        public function run($dbg=false){

          $this->running = $this->__init($dbg);
          print("\nRunning : " . basename(__FILE__, '.php') . "\n");
          while ( $this->running === true ) {
            $this->tick();
            usleep($this->timeToNextTick());
          }
          print("\nEnding : " . basename(__FILE__, '.php') . "\n");
        }

    }




  $numberstation = new PiServiceNumberstation();

  try {
    $numberstation->run();
  }
  catch(Exception $e) {
    print(get_class($e) . ": " . $e->getMessage() . "\n");
  }


?>