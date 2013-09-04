<?php

    /**
     * π.service.time
     * 
     * The pi time service, a server that emits time signals 
     * which can be subscribed to by other services
     *
     * This is part of the backbone of our application server
     * It needs to be extra bullet-proof
     *
     * @author Johan Telstad, jt@enfield.no, 2011-2013
     *
     */

    require_once("pi.service.php");


    class PiServiceTime extends PiService {

        private   $debug            = false;

        private   $subscribercount  = -1;
        private   $running          = false;
        private   $ticks            = 0;
        private   $ticklength       = 0;

        protected $address          = "";
        protected $name             = "";




        public function __construct() {
          $this->address    = basename(__FILE__, '.php');
          $this->name       = $this->address;
        }



        /**
         *
         * π.service.time.heartbeat()
         *
         * Emits a heartbeat event to subscribers at a rate of HEARTBEATS_PER_SECOND,
         * as defined in pi.config.php
         *
         * Also emits a time event every second, giving the current server time
         * as a float, where the whole part represents a standard unix timestamp
         *
         */


        private function heartbeat() {

          $this->subscribercount = $this->pubsub->publish('pi.service.time.heartbeat', null);

          // emit time in microseconds once per second
          if( $this->ticks++ % HEARTBEATS_PER_SECOND === 0 ) {
            $this->pubsub->publish('pi.service.time', microtime(true));
            if($this->debug) {
              print("\r".microtime(true));
            }

            // emit an each.minute event every whole minute
            if( ($this->ticks % (HEARTBEATS_PER_SECOND*60)) === 0 ) {
              $this->pubsub->publish('pi.service.time.each.minute', microtime(true));
              if($this->debug) {
                print( "\n" . date("D, d M Y H:i:s") . "\n" );
              }

            // emit an each.hour event every whole hour
              if( (time() % (HEARTBEATS_PER_SECOND*3600)) === 0 ) {
                $this->pubsub->publish('pi.service.time.each.hour', microtime(true));
              }
            }
          }
        }


        private function timeToNextHeartbeat() {

          // round up to nearest whole tick
          $nextbeat = ceil(microtime(true) * HEARTBEATS_PER_SECOND)/HEARTBEATS_PER_SECOND;

          // return difference in microseconds 
          // we call microtime *again* on exit, for accuracy
          return floor( A_COOL_MILLION * ($nextbeat - microtime(true)) );
        }




        public function run($dbg=false){

          if($dbg) {
            print("\nRunning : " . basename(__FILE__, '.php') . "\n");
          }

          $this->running  = true;
          $this->debug    = $dbg;


          if($dbg) {
            print("Waiting for next heartbeat...");
          }
          // wait for next heartbeat
          usleep($this->timeToNextHeartbeat());
          if($dbg) {
            print("done!\n");
          }

          // a small cheat to align our tick counter to 
          // whole minutes and seconds from the get-go
          $this->ticks = HEARTBEATS_PER_SECOND * (time() % 60);

          if($dbg) {
            print("Starting at " . $this->ticks . " ticks.\n");
          }

          while ( $this->running === true ) {
            $this->heartbeat();
            usleep($this->timeToNextHeartbeat());
          }
        }

    }




  $time = new PiServiceTime();

  try {
    $time->run(true);
  }
  catch(Exception $e) {
    $print(get_class($e) . ": " . $e->getMessage() . "\n");
  }


?>
