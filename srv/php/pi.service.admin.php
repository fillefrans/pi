<?php

    /**
     * pi.service.admin
     * 
     * The pi admin service, a server that gives  
     * administrators the power to monitor and control
     * everything that happens on the pi server
     *
     * 
     * This is part of the backbone of the pi server
     *
     * @author Johan Telstad, jt@enfield.no, 2011-2013
     *
     */


    require_once("pi.service.php");


    class PiServiceAdmin extends PiService {

        private   $debug            = false;

        private   $subscribercount  = -1;
        private   $running          = false;
        private   $ticks            = -1;
        private   $ticklength       = 0;

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

          if(!$this->__init($dbg)) {
            $this->quit("__init() returned false, aborting run().");
          }
          print("\nRunning : " . basename(__FILE__, '.php') . "\n");
        }

    }




  $admin = new PiServiceAdmin();

  try {
    $admin->run();
  }
  catch(Exception $e) {
    print(get_class($e) . ": " . $e->getMessage() . "\n");
  }


?>