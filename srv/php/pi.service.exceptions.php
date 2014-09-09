<?php

    /**
     * π.channel.exceptions
     * 
     * The pi exception channel, a sink that receives all 
     * exceptions coming in from apps 
     *
     * This is part of the pi server
     *
     * @author Johan Telstad, jt@viewshq.no, 2011-2014
     *
     */

    require_once("pi.channel.php");




    // HOW THE EXCEPTION OBJECT LOOKS

    //   exception = { message: message, url: url, linenumber: linenumber, timestamp: (new Date().getTime()) },
    //   signature = (url + linenumber).replace([^a-zA-Z0-9], "_");

    // if(self.history.length > self.MAX_ERRORS) {
    //   exception.MAX_ERRORS_HIT  = true;
    //   exception.errorcount      = self.MAX_ERRORS;
    //   exception.signaturecount  = self._signatures.length;

    // WHAT IT MEANS
    // 
    // signaturecount is the number of unique errors counted
    // errorcount is the total number of errors
    // MAX_ERRORS_HIT is set when the client handler detaches itself,
    // so it will only be set on the very last error






    class PiChannelExceptions extends PiChannel {

        private   $debug            = false;

        protected $subscribercount  = -1;
        protected $running          = false;
        protected $ticks            = -1;
        protected $ticklength       = 0;

        protected $address          = "";
        protected $name             = "";




        public function __construct() {
          $this->address  = basename(__FILE__, '.php');
          $this->name     = $this->address;
        }


        private function __init($dbg=false) {
          $this->name = $this->address;

          try {
            $this->pubsub->subscribe(['error.*'], [$this, 'onException']);
          }
          catch(Exception $e) {
            return false;
          }

        }


        // the redis subscription callback
        public function onException($redis, $address, $message, $event="error") {
          print("$event@$address: $message (" . time() . ")\n");
        }


        private function quit($msg="No message. Goodbye.") {

          die($msg);
        }




        public function run($dbg=false){

          $this->running = $this->__init($dbg);

          print("\nStarted : " . basename(__FILE__, '.php') .  ", result:  " . $this->running . "\n");

          return $this->running;
        }

    }




  $exceptions = new PiChannelExceptions();

  try {
    $exceptions->run();
  }
  catch(Exception $e) {
    print(get_class($e) . ": " . $e->getMessage() . "\n");
  }


?>
