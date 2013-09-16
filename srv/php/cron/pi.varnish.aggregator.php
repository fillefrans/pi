<?php


  /**
   *  π.varnish.aggregator
   *
   *  A script that reads lines from varnishlog through stdin
   *  and aggregates the data across keys and time
   *
   *  @author Johan Telstad <jt@enfield.no>, 2011-2013
   *  
   */




  declare( ticks = 16 );


  $aggregator = null;


  // Interrupt handling

  pcntl_signal( SIGTERM, "signalhandler" );
  pcntl_signal( SIGINT,  "signalhandler" );


  function signalhandler( $signal ){
    global $aggregator;

    switch( $signal ) {
       
      case SIGTERM:
        print( "Received shutdown signal, shutting down now...\n" );
        $aggregator->flushdata();
        exit;
     
      case SIGINT:
        print( "Interrupted, quitting...\n" );
        $aggregator->flushdata();
        exit;
    }
  }


  require_once( __DIR__ . '/../pi.php');




  class PiVarnishAggregator extends Pi {

    // config

    protected $address = "pi.varnish.aggregator";

    private $outputdir = LOG_DIR;
    private $logfile   = "";

    // in seconds
    private $timetorun = 3600;  


    // initialize variables
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


    function floorHour($time){
    	return floor($time/3600)*3600;
  	}

    function secondsToNextHour($time){
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



    function printreport(){
    		
    		$this->dataset["loglinesread"]		= $this->linecounter;
    		$this->dataset["events"]					= $this->eventcounter;
    		$this->dataset["views"]						= $this->viewcounter;
    		$this->dataset["skipped"]					= $this->eventsskipped;
    		$this->dataset["memused"]					= memory_get_usage();
    		$this->dataset["memmaxused"]			= memory_get_peak_usage();
    		$this->dataset["memmaxallocated"]	= memory_get_peak_usage(true);
    		$this->dataset["stop"]						= time();
    		$this->objects["dataset"]					= $this->dataset;


    		print( Date("His:\n"));
        print( "\tLines read:\t$this->linecounter\n" );
        print( "\tEvents read:\t$this->eventcounter\n" );
        print( "\tViews read:\t" . ( $this->viewcounter ) . "\n" );
    		print( "\tSkipped:\t$this->eventsskipped\n" );

        var_dump( $this->dataset );
    }




    public function flushdata(){

        if($this->fp) {
          fclose($this->fp);
        }

        $this->printreport();
      
        
    		if( count( $this->objects ) > 0 ){ 
    	  	echo "\nflushing views to ./views-log.json ...";
        	if( file_put_contents("$this->outputdir/$this->fileprefix.views-log.json", json_encode( $this->objects )))
            echo "done!\n";
        	else
            echo "error!  Unable to flush data to file.\n";
      		}
    	
    		if( count( $this->events ) > 0 ){ 
    	  	echo "\nflushing events to ./events-log.json ...";
        
        	if( file_put_contents( "$this->outputdir/$this->fileprefix.events-log.json", json_encode( $this->events )))
            echo "done!\n";
        	else
            echo "error!  Unable to flush data to file.\n";
     			}
       }


    public function onTick(){

      $this->say("tick!");

    }


    public function run() {
      /**   MAIN   **/

      if(!$this->__init()) {
        return false;
      }


      $this->fp = fopen( $this->logfile, "w");

      $this->subscribe("pi.service.time.tick", array($this,"onTick"));

      while( true ){

          $line = fgets( STDIN );
          if(($this->linecounter & 3) === 3){
            if(time()>$this->stoptime){
              print("we have run our designated time of $timetorun seconds, clean up and exit\n");
              $this->flushdata();
              die(0);
              }
          }
      //    fwrite($fp, $line);
          //var_dump($logline);
          $this->linecounter++;


          if (( strpos( $line, "SessionOpen", 6 )) === 6 ) {
              list( $ip, $counter, $port ) = explode( " ", substr( $line, 21 ));
              if( $this->skipevent ){ 
                $this->eventsskipped++;
              }
              $this->skipevent = false;
          }
          
          elseif ( strpos( $line, "RxURL", 6) === 6 ) {
              $urlarray = explode( "/", substr( $line, 22 ));
              if( count( $urlarray ) < 5 ){
                print("Skipping event: $line\n");
                $this->skipevent = true;
                continue;
                }
              else{
                } 
            
              $elements         = count( $urlarray ) >> 1;
              $this->currobjectid  = $urlarray[1];
              $this->curreventtype = $urlarray[0];
          
              unset( $this->params ); // Just-in-Time
        
              while( $elements-- > 1 ){ // 1 => skip the two first elements, they are objectid and eventtype
                $this->params[$urlarray[$elements << 1]] = $urlarray[( $elements << 1 ) + 1];  // create assoc
                }
              unset($this->params['cb']);  // cachebuster param
              unset($this->params['s']);  // 
              }
          
          elseif ( strpos( $line, "ReqEnd", 6) == 6 ) {
            if( $this->skipevent ){
              print("Skipping line: $line\n");
              continue;
              }

            $timeline = substr( $line, 21 );
            list( $XID, $startproc, $endproc, $reqtime, $resptime, $deliverytime ) = explode( " ", $timeline );
            $paramvalues = "";
           
            if( !isset( $this->objects[$this->curreventtype][$this->currobjectid] )){ 

              //no views/events yet on this objectid
              $this->objects[$this->curreventtype][$this->currobjectid]['count'] = 1;
              if( $this->curreventtype != "v" ){
                  if($this->curreventtype=="e"){
                  $this->eventcounter++;
                  $this->params['ip']   = $ip;
                  $this->params['time'] = $startproc;
                  $this->params['objectid'] = $currobjectid;
                  
                  // add to events array.
                  $this->events[] = $this->params;
      //            unset( $params['objectid'] );
                  }
                else{
                  print("Invalid eventtype: $curreventtype\nurl: $line\n");
                  continue;
                  }
                  
                }
              else{
                $this->viewcounter++;
                }
              foreach( $this->params as $key => $value ){
                $this->objects[$this->curreventtype][$this->currobjectid][$key][$value]=1;
                }
              }
            else {

              // Count it
              $this->objects[$this->curreventtype][$this->currobjectid]['count']++;
              if( $this->curreventtype != "v" ){
                if($this->curreventtype=="e"){
                  $this->eventcounter++;
                  $this->params['ip']   = $ip;
                  $this->params['time'] = $startproc;
                  $this->params['objectid'] = $this->currobjectid;
                  
                  // add to events array.
                  $this->events[] = $this->params;
                  }
              else{
                  print("Invalid eventtype: $this->curreventtype\nurl: $line\n");
                  continue;
                  }
                unset( $this->params['objectid'] );
                          
                }
              else{
                $this->viewcounter++;
                //print ( "counting: $line\n" );
                }
              foreach( $this->params as $key => $value ){
                if( isset( $this->objects[$this->curreventtype][$this->currobjectid][$key][$value] )){
                  //Exists, so inc counter
                  $this->objects[$this->curreventtype][$this->currobjectid][$key][$value]++;
                  }
                else{
                  // First of its kind
                  $this->objects[$this->curreventtype][$this->currobjectid][$key][$value] = 1;
                  }
                }
              }

          //update screen

          if( DEBUG ){ // every x event

            $memusage       = memory_get_usage();
            $gccycles       = gc_collect_cycles(); // force garbage collection
            $timecomponents = explode( ".", $startproc );
            if( count( $timecomponents ) > 1 ){
              $time  = strftime( "%T", (int) $timecomponents[0] ) . "." . substr( $timecomponents[1], 0, 3 );
              }
            echo "\r$time\tviews:".number_format( $this->viewcounter ) . "\tEvents:\t" . 
            number_format( $this->eventcounter ) . "\tmem:\t$memusage\t$ip $this->curreventtype:$this->currobjectid". 
            ( DEBUG ? "\n" : "" );
            }

          unset( $this->currobjectid  );  
          unset( $this->curreventtype );
        }
      }

    }

  }


  $aggregator = new PiVarnishAggregator();

  try {
    $aggregator->run();
  }
  catch(Exception $e) {
    print(get_class($e) . ": " . $e->getMessage()."\n");
  }

?>