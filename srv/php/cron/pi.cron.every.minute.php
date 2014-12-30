<?php

    /**
     * π.cron.every.minute
     * 
     * This script is run by cron every minute
     *
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



        private function quit($msg="Goodbye.\n") {

          die($msg);
        }


        public function run($dbg=false){

          $this->__init();
          // print("Running : " . basename(__FILE__, '.php') . "\n");
          $this->publish($this->address, "Crontab running: " . basename(__FILE__));

          $directory  = __DIR__;
          $basename   = basename(__FILE__, '.php') .".d";
          $path       = $directory . DIRECTORY_SEPARATOR . $basename;

          // include script files from subdir
          $this->includeScripts($path);
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