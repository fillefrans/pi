<?php

    /**
     * π.cron.every.minute
     * 
     * This script is run by cron every minute
     *
     * Start batch scripts as necessary by
     * adding to the run() method
     * 
     * This class is part of the pi server
     *
     * @author Johan Telstad, jt@viewshq.no, 2011-2014
     *
     */

    require_once("pi.cron.php");


    class PiCronEveryMinute extends PiCron {

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
          // print("Running : " . basename(__FILE__, '.php') . "\n");
          $this->publish("Crontab running: " . basename(__FILE__));
          // $this->quit();
        }

    }




  $crontab = new PiCronEveryMinute();

  try {
    $crontab->run();
  }
  catch(Exception $e) {
    print(get_class($e) . ": " . $e->getMessage() . "\n");
  }


?>