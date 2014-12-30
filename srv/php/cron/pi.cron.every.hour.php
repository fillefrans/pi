<?php

    /**
     * pi.cron.every.hour
     * 
     * This script is run by cron every hour
     * Start batch scripts as necessary by adding script files to the correct 
     * subfolder (e.g [pi-root]/srv/php/cron/pi.cron.every.xxx.d/)
     * Where "xxx" is from the name of the current script file
     *
     * This class is part of the pi server
     *
     * @author Johan Telstad, jt@viewshq.no, 2011-2014
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
          $this->publish($this->address, "Crontab running: " . basename(__FILE__));

          $directory  = __DIR__;
          $basename   = basename(__FILE__, '.php') .".d";
          $path       = $directory . DIRECTORY_SEPARATOR . $basename;


          $this->includeScripts($path);

          $this->quit();
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