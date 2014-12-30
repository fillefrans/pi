<?php

    /**
     * pi.cron.every.midnight
     * 
     * This script is run by cron every midnight
     * 
     * Add batch scripts as necessary by adding script files to the correct 
     * subfolder (e.g [pi-root]/srv/php/cron/pi.cron.every.xxx.d/)
     * Where "xxx" is from the name of the current script file
     *
     * This class is part of the pi server
     *
     * @author Johan Telstad, jt@viewshq.no, 2011-2014
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



        private function quit($msg = "Goodbye.\n") {

          die($msg);
        }



        public function run($dbg = false){

          $this->__init();
          print("\nRunning : " . basename(__FILE__, '.php') . "\n");
          $this->publish($this->address, "Crontab running: " . basename(__FILE__));

          $directory  = __DIR__;
          $basename   = basename(__FILE__, '.php') .".d";
          $path       = $directory . DIRECTORY_SEPARATOR . $basename;

          $this->includeScripts($path);

          $this->quit();
        }

    } // class PiCronEveryMidnight




  $crontab = new PiCronEveryMidnight();

  try {
    $crontab->run();
  }
  catch(Exception $e) {
    print(get_class($e) . ": " . $e->getMessage() . "\n");
  }


?>