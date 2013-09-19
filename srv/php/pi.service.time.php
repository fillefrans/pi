<?php

    /**
     * π.service.time
     * 
     * The pi time service, a server that emits time signals 
     * which can be subscribed to by other services
     *
     * This is part of the backbone of the pi server
     *
     * @author Johan Telstad, jt@enfield.no, 2011-2013
     *
     */

    require_once("pi.service.php");


    class PiServiceTime extends PiService {

        private   $debug            = false;

        protected $subscribercount  = -1;
        protected $running          = false;
        protected $ticks            = -1;
        protected $ticklength       = 0;

        protected $address          = "";
        protected $name             = "";




        public function __construct() {
          $this->address    = basename(__FILE__, '.php');
          $this->name       = $this->address;
        }



        /**
         *
         * π.service.time.tick()
         *
         * Emits a tick event to subscribers at a rate of TICKS_PER_SECOND,
         * as defined in pi.config.php
         *
         * Also emits a time event every second, giving the current server time
         * as a float, where the whole part represents a standard unix timestamp
         *
         */


        protected function tick() {

          $now = microtime(true);

          $this->subscribercount = $this->pubsub->publish('pi.service.time.tick', $this->ticks . " : " . $this->subscribercount);

          // emit time in microseconds once per second
          if( ++$this->ticks % TICKS_PER_SECOND === 0 ) {
            $this->pubsub->publish('pi.service.time', $now);
            // print("second: " . $now);

            // emit an each.minute event every whole minute
            if( ($this->ticks % (TICKS_PER_SECOND*60)) === 0 ) {

              // Is this the very first run?
              if( $this->ticks === 0 ) {

                // a small cheat to align our tick counter to 
                // whole minutes and seconds from the get-go:
                // initialize the ticks variable to the number 
                // of ticks since the previous midnight
                $this->ticks = TICKS_PER_SECOND * ( time() % SECONDS_IN_A_DAY );
              }
              else {
                $this->pubsub->publish('pi.service.time.each.minute', $now);
                // print("minute: " . $now);
              }

              // emit an each.hour event every whole hour
              if( (time() % 3600) === 0 ) {
                if( ($this->ticks > 600) && ((time() % SECONDS_IN_A_DAY) === 0) ) {
                  $this->quit("\n\npi.service.time : We have run until midnight, stopping.\n\n");
                }
                $this->pubsub->publish('pi.service.time.each.hour', $now);
              }

            }

          }

        }


        private function timeToNextTick() {

          // round up to nearest whole tick
          $nexttick = ceil(microtime(true) * TICKS_PER_SECOND)/TICKS_PER_SECOND;

          // return difference in microseconds 
          // we call microtime *again* on exit, for accuracy
          return floor( A_COOL_MILLION * ($nexttick - microtime(true)) );
        }


        private function quit($msg="No message. Goodbye.") {

          die($msg);

        }




        public function run($dbg=false){

          $this->__init();
          print("\nRunning : " . basename(__FILE__, '.php') . "\n");

          $this->running  = true;

          while ( $this->running === true ) {
            $this->tick();
            usleep($this->timeToNextTick());
          }
        }

    }




  $time = new PiServiceTime();

  try {
    $time->run();
  }
  catch(Exception $e) {
    print(get_class($e) . ": " . $e->getMessage() . "\n");
  }


?>
