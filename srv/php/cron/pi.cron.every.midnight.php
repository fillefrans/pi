<?php

    /**
     * π.cron.every.midnight
     * 
     * This script is run by cron every midnight
     *
     * This class is part of the pi server
     *
     * @author Johan Telstad, jt@enfield.no, 2011-2013
     *
     */

    require_once("pi.cron.php");


    class PiCronEveryMidnight extends PiCron {

        private   $debug            = false;

        private   $subscribercount  = -1;
        private   $running          = false;

        protected $address          = "";
        protected $name             = "";




        public function __construct() {
          $this->address    = basename(__FILE__, '.php');
          $this->name       = $this->address;
        }



        private function quit($msg="Goodbye. No message.\n") {

          die($msg);

        }


        public function run($dbg=false){

          $this->__init();
          print("\nRunning : " . basename(__FILE__, '.php') . "\n");
          $this->publish("Crontab running: " . basename(__FILE__));
          $this->quit();
        }

    }




  $crontab = new PiCronEveryMidnight();

  try {
    $crontab->run();
  }
  catch(Exception $e) {
    print(get_class($e) . ": " . $e->getMessage() . "\n");
  }


?>