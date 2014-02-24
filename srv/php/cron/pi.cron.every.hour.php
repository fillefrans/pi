<?php

    /**
     * pi.cron.every.hour
     * 
     * This script is run by cron every hour
     * Start batch scripts as necessary by
     * adding to the run() method
     *
     * This class is part of the pi server
     *
     * @author Johan Telstad, jt@enfield.no, 2011-2013
     *
     */

    require_once("pi.cron.php");


    class PiCronEveryHour extends PiCron {

        private   $debug            = false;

        private   $subscribercount  = -1;
        private   $running          = false;

        protected $starttime        = 0;
        protected $address          = "";
        protected $name             = "";




        public function __construct() {
          $this->starttime  = microtime(true);
          $this->address    = basename(__FILE__, '.php');
          $this->name       = $this->address;
        }



        private function quit($msg="Goodbye. No message.\n") {

          die($msg);

        }


        public function run($dbg=false){

          $this->__init();
          // print("Running : " . basename(__FILE__, '.php') . "\n");
          $this->publish("Crontab running: " . basename(__FILE__));
          passthru(  'php ' . __DIR__ . '/pi.tracs.incoming.php');
          // $this->quit();
        }

    }




  $crontab = new PiCronEveryHour();

  try {
    $crontab->run();
  }
  catch(Exception $e) {
    print(get_class($e) . ": " . $e->getMessage() . "\n");
  }


?>