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
     * @author Johan Telstad, jt@viewshq.no, 2011-2014
     *
     */


    require_once("pi.service.php");


    class PiServiceAdmin extends PiService {

        private   $debug            = false;

        private   $running          = false;
        private   $ticks            = -1;
        private   $ticklength       = 0;
        private   $msgcount         = 0;

        protected $subscribercount  = -1;
        protected $address          = "";
        protected $name             = "";




        public function __construct() {
          $this->name    = basename(__FILE__, '.php');
          $this->address = "ctrl." . $this->name;
        }


        private function init($dbg=false) {
          return (
            $this->pubsub->subscribe([$this->address], array($this, 'onCtrlMessage')) 
            &&  
            $this->pubsub->subscribe([$this->name], array($this, 'onMessage'))
          );
        }


        private function quit($msg="Goodbye. No message.") {
          die($msg);
        }


        public function onCtrlMessage($redis, $chan, $msg, $event="data") {

          // strip out newlines
          $message  = str_replace("\n", "", $msg);

          $this->msgcount++;
          $this->say($chan . " : " . $message);
        }


        public function onMessage($redis, $chan, $msg, $event="data") {

          // strip out newlines
          $message  = str_replace("\n", "", $msg);

          $this->msgcount++;
          $this->say($chan . " : " . $message);
        }



        public function run($dbg=false){

          if(!$this->__init($dbg)) {
            $this->quit("__init() returned false, aborting run().");
          }
          if(!$this->init($dbg)) {
            $this->quit("init() returned false, aborting run().");
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