<?php

    /**
     * π.service.tasks
     * 
     * The pi task service, a server that controls task 
     * execution, queueing and control
     *
     * This is part of the backbone of the pi server
     *
     * @author Johan Telstad, jt@viewshq.no, 2011-2014
     *
     */

    require_once("pi.service.php");


    class PiServiceTasks extends PiService {

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




  $tasks = new PiServiceTasks();

  try {
    $tasks->run();
  }
  catch(Exception $e) {
    print(get_class($e) . ": " . $e->getMessage() . "\n");
  }


?>