<?php

    /**
     * The pi task service, a server that listens for tasks
     * and executes them as they are requested.
     *
     * progress is reported back through the task's unique address
     * 
     *
     * This is part of the backbone of our application server
     * It needs to be extra bullet-proof
     *
     * @author Johan Telstad, jt@enfield.no, 2011-2013
     *
     */

    require_once(PI_ROOT . "pi.service.php");


    class PiServiceTasks extends PiService {

      protected $address = basename(__FILE__,'.php');


        // handle incoming pubsub messages from redis
        public function onMessage(){
          $this->incoming++;
          $this->lastactivity = microtime(true);
          $message = json_decode($msg->getData(), true);
 
          switch ($message['command']) {
            case 'peek':
              $this->query($message);
              break;
            case 'subscribe':
              $this->subscribe($message); 
              break;
            case 'unsubscribe': 
              $this->unsubscribe($message); 
              break;
            case 'publish':
              $this->publish($this->channel, $message);
              break;
            case 'queue':
              $this->handleQueueRequest($message);
              break;
            case 'setbit':
            case 'getbit':
            case 'set':
            case 'get':
            case 'lpop':
            case 'rpop':
              $this->redisCommand($message);
              break;
            case 'quit':
              $this->reply($message, "Goodbye.", 1);
              die("Client sent 'quit' command. Exiting.");
              break;
            default:
              $this->reply($message, "Unknown command: '{$message['command']}'", 0, "error");
              break;
          }
        }

        public function run(){
      		$this->say("\n" . get_class($this) . ": running\n");
          $this->__init();
        }
    }




  $service = new PiServiceTasks();

  try {
    $service->run();
  }
  catch(Exception $e) {
    $this->say(get_class($e) . ": " . $e->getMessage() . "\n");
  }


?>
