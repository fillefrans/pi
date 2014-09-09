<?php


  /**
   *  π.varnish.aggregator
   *
   *  A script that reads lines from varnishlog through stdin
   *  and aggregates the data across keys and time
   *
   *  @author Johan Telstad <jt@viewshq.no>, 2011-2014
   *  
   */




  declare(ticks = 16);

  $aggregator = null;


  // error_reporting(E_ALL )

  // Reporting E_NOTICE can be good too (to report uninitialized
  // variables or catch variable name misspellings ...)
  error_reporting(E_ERROR | E_PARSE | E_NOTICE);


  // Interrupt handling

  pcntl_signal(SIGTERM, "signalhandler");
  pcntl_signal(SIGINT,  "signalhandler");


  function signalhandler($signal) {
    global $aggregator;

    switch ($signal) {
       
      case SIGTERM:
        print("Received shutdown signal, shutting down now...\n");
        $aggregator->flushdata();
        exit;
     
      case SIGINT:
        print("Interrupted, quitting...\n");
        $aggregator->flushdata();
        exit;
    }
  }


  require_once(__DIR__ . '/../pi.php');




  class VarnishAggregator extends Pi {

    // config

    protected $address = "pi.varnish.aggregator";

    private $outputdir = LOG_DIR;
    private $logfile   = "";

    /** @todo  Read time to run from command line  */
    
    private $timetorun = 3600;  // in seconds

    private $linecounter	 = 0;
    private $eventcounter	 = 0;
    private $viewcounter	 = 0;
    private $eventsskipped = 0;

    private $objects	     = null;
    private $events 	     = null;

    private $currobjectid  = null;
    private $curreventtype = null;

    private $skipevent 		 = false;
    private $skipcounter	 = 0;

    private $dataset       = null;
    private $stoptime      = 0;
    private $fileprefix    = null;


    function floorHour($time) {
    	return floor($time/3600)*3600;
  	}

    function secondsToNextHour($time) {
    	return 3600 - ($time % 3600);
  	}


    protected function __init() {

      $this->outputdir  = LOG_DIR;
      $this->logfile    = $this->outputdir . basename(__FILE__, '.php') . ".log";
      $this->dataset    = array('start' => time());
      $this->stoptime   = $this->dataset['start'] + $this->timetorun;
      $this->fileprefix = Date("Ymd.His", $this->dataset['start'])."-".Date("His",$this->stoptime);

      return true;
    }



    function printreport() {
    		
    		$this->dataset["loglinesread"]		= $this->linecounter;
    		$this->dataset["events"]					= $this->eventcounter;
    		$this->dataset["views"]						= $this->viewcounter;
    		$this->dataset["skipped"]					= $this->eventsskipped;
    		$this->dataset["memused"]					= memory_get_usage();
    		$this->dataset["memmaxused"]			= memory_get_peak_usage();
    		$this->dataset["memmaxallocated"]	= memory_get_peak_usage(true);
    		$this->dataset["stop"]						= date("d.m H:i:s");
    		$this->objects["dataset"]					= $this->dataset;


    		print(Date("d.m H:i:s:\n"));
        print("\tLines read:\t$this->linecounter\n");
        print("\tEvents read:\t$this->eventcounter\n");
        print("\tViews read:\t" . ($this->viewcounter) . "\n");
    		print("\tSkipped:\t$this->eventsskipped\n");

        var_dump($this->dataset);
    }




    public function flushdata() {

        if ($this->fp) {
          fclose($this->fp);
        }

        $this->printreport();
      
        
    		if (count($this->objects) > 0) { 
    	  	echo "\nflushing views to ./views-log.json ...";
        	if (file_put_contents("$this->outputdir/$this->fileprefix.views-log.json", json_encode($this->objects)))
            echo "done!\n";
        	else
            echo "error!  Unable to flush data to file.\n";
      		}
    	
    		if (count($this->events) > 0) { 
    	  	echo "\nflushing events to ./events-log.json ...";
        
        	if (file_put_contents("$this->outputdir/$this->fileprefix.events-log.json", json_encode($this->events)))
            echo "done!\n";
        	else
            echo "error!  Unable to flush data to file.\n";
     			}
       }





    public function onTick() {

      $this->dataset["loglinesread"]    = $this->linecounter;
      $this->dataset["events"]          = $this->eventcounter;
      $this->dataset["views"]           = $this->viewcounter;
      $this->dataset["skipped"]         = $this->eventsskipped;
      $this->dataset["memused"]         = memory_get_usage();
      $this->dataset["memmaxused"]      = memory_get_peak_usage();
      $this->dataset["memmaxallocated"] = memory_get_peak_usage(true);

      $this->publish(json_encode($this->dataset));
    }


    public function run() {
      /**   MAIN   **/

      if (!$this->__init()) {
        $this->say("init returned false");
        return false;
      }

      // print("Running");

      $this->fp = fopen($this->logfile, "w");

      // $this->subscribe("pi.service.time.tick", array($this,"onTick"));

      while (true) {

          $line = fgets(STDIN);
          if (($this->linecounter & 3) === 3) {
            if (time()>$this->stoptime) {
              print("we have run our designated time of $timetorun seconds, clean up and exit\n");
              $this->flushdata();
              die(0);
              }
          }
      //    fwrite($fp, $line);
          //var_dump($logline);
          $this->linecounter++;


          if(strlen($line)<21) {
            continue;
          }
          if ((strpos($line, "SessionOpen", 6)) === 6) {
              list($ip, $counter, $port) = explode(" ", substr($line, 21));
              if ($this->skipevent) { 
                $this->eventsskipped++;
              }
              $this->skipevent = false;
          }
          
          elseif (strpos($line, "RxURL", 6) === 6) {
              $urlarray = explode("/", substr($line, 22));
              if (count($urlarray) < 3) {
                print("Skipping event: $line\n");
                $this->skipevent = true;
                continue;
                }
              else{
                } 
            
              // foreach ($urlarray as $key=>$value) {
              //   print("$key : $value\n");
              // }

              $message = urldecode($urlarray[2]);
              print(" {$urlarray[1]} : {$message}");

              $this->params = json_decode($message, true);
              $this->publish($urlarray[1], $message);

              // $elements = count($urlarray) * 2;
              // $this->currobjectid  = $urlarray[1];
              // $this->curreventtype = $urlarray[0];
          
              // unset($this->params); // Just-in-Time
        
              // while($elements-- > 1) { // 1 => skip the two first elements, they are objectid and eventtype
              //   $this->params[$urlarray[$elements << 1]] = $urlarray[($elements << 1) + 1];  // create assoc
              //   }
              // unset($this->params['cb']);  // cachebuster param
              // unset($this->params['s']);  // 


              }
          
          elseif (strpos($line, "ReqEnd", 6) == 6) {
            if ($this->skipevent) {
              print("Skipping line: $line\n");
              continue;
              }

            // $timeline = substr($line, 21);
            // list($XID, $startproc, $endproc, $reqtime, $resptime, $deliverytime) = explode(" ", $timeline);
            // $paramvalues = "";
           
            // if (!isset($this->objects[$this->curreventtype][$this->currobjectid])) { 

            //   //no views/events yet on this objectid
            //   $this->objects[$this->curreventtype][$this->currobjectid]['count'] = 1;
            //   if ($this->curreventtype != "v") {
            //       if ($this->curreventtype=="e") {
            //       $this->eventcounter++;
            //       $this->params['ip']   = $ip;
            //       $this->params['time'] = $startproc;
            //       $this->params['objectid'] = $currobjectid;
                  
            //       // add to events array.
            //       $this->events[] = $this->params;
            //       // unset($params['objectid']);
            //       }
            //     else{
            //       print("Invalid eventtype: $curreventtype\nurl: $line\n");
            //       continue;
            //       }
                  
            //     }
            //   else{
            //     $this->viewcounter++;
            //     }
            //   foreach($this->params as $key => $value) {
            //     $this->objects[$this->curreventtype][$this->currobjectid][$key][$value]=1;
            //     }
            //   }
            // else {

            //   // Count it
            //   $this->objects[$this->curreventtype][$this->currobjectid]['count']++;
            //   if ($this->curreventtype != "v") {
            //     if ($this->curreventtype=="e") {
            //       $this->eventcounter++;
            //       $this->params['ip']   = $ip;
            //       $this->params['time'] = $startproc;
            //       $this->params['objectid'] = $this->currobjectid;
                  
            //       // add to events array.
            //       $this->events[] = $this->params;
            //       }
            //   else{
            //       print("Invalid eventtype: $this->curreventtype\nurl: $line\n");
            //       continue;
            //       }
            //     unset($this->params['objectid']);
                          
            //     }
            //   else{
            //     $this->viewcounter++;
            //     }
            //   foreach($this->params as $key => $value) {
            //     if (isset($this->objects[$this->curreventtype][$this->currobjectid][$key][$value])) {
            //       //Exists, so inc counter
            //       $this->objects[$this->curreventtype][$this->currobjectid][$key][$value]++;
            //       }
            //     else{
            //       // First of its kind
            //       $this->objects[$this->curreventtype][$this->currobjectid][$key][$value] = 1;
            //       }
            //     }
            //   }

          //update screen

          // if (DEBUG) { 

          //   $memusage       = memory_get_usage();
          //   $gccycles       = gc_collect_cycles(); // force garbage collection
          //   $timecomponents = explode(".", $startproc);
          //   if (count($timecomponents) > 1) {
          //     $time  = strftime("%T", (int) $timecomponents[0]) . "." . substr($timecomponents[1], 0, 3);
          //     }
          //   echo "\r$time\tviews:".number_format($this->viewcounter) . "\tEvents:\t" . 
          //   number_format($this->eventcounter) . "\tmem:\t$memusage\t$ip $this->curreventtype:$this->currobjectid";
          //   }

          unset($this->currobjectid );  
          unset($this->curreventtype);
        }
      }

    }



  }


  $aggregator = new VarnishAggregator();

  try {
    // print("run");
    $aggregator->run();
  }
  catch(Exception $e) {
    print(get_class($e) . ": " . $e->getMessage()."\n");
  }

?>