<?php

    /**
     * π.cron
     * 
     * Base class for Cron scripts
     *
     * This is part of the backbone of the pi server
     *
     * @author Johan Telstad, jt@viewshq.no, 2011-2014
     *
     */

    require_once( __DIR__ . "/../pi.php");


    class PiCron extends Pi {

        protected $address          = "";
        protected $name             = "";




        public function __construct() {
          $this->address    = basename(__FILE__, '.php');
          $this->name       = $this->address;
        }


        private function quit($msg="Goodbye. No message.") {

          die($msg);

        }




        public function run($dbg=false){

          // $this->__init();
          print("\nRunning : " . basename(__FILE__, '.php') . "\n");

        }

    }


?>