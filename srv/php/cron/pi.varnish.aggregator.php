<?php

  declare( ticks = 16 );


  require_once( __DIR__ . '/pi.php');


  // config
  $outputdir = LOG_DIR;
  $logfile   = $outputdir . basename(__FILE__, '.php') . ".log";

  // in seconds
  $timetorun = 3600;  


  // initialize variables
  $linecounter		= 0;
  $eventcounter		= 0;
  $viewcounter		= 0;
  $eventsskipped	= 0;

  $objects	= null;
  $events 	= null;


  $currentobjectid  = null;
  $currenteventtype = null;

  $skipevent 		= false;
  $skipcounter	= 0;

  $dataset['start'] = time();
  $stoptime = $dataset['start'] + $timetorun;
  $fileprefix = Date("Ymd.His", $dataset['start'])."-".Date("His",$stoptime);


  function floorHour($time){
  	return floor($time/3600)*3600;
  	}

  function secondsToNextHour($time){
  	return 3600 - ($time % 3600);
  	}



  // Interrupt handling

  pcntl_signal( SIGTERM, "signalhandler" );
  pcntl_signal( SIGINT,  "signalhandler" );


  function printreport(){

      global 	$linecounter, $dataset, $objects, 
  						$viewcounter, $eventcounter, $eventsskipped, $skipcounter;
  		
  		$dataset["loglinesread"]		= $linecounter;
  		$dataset["events"]					= $eventcounter;
  		$dataset["views"]						= $viewcounter;
  		$dataset["skipped"]					= $eventsskipped;
  		$dataset["memused"]					= memory_get_usage();
  		$dataset["memmaxused"]			= memory_get_peak_usage();
  		$dataset["memmaxallocated"]	= memory_get_peak_usage(true);
  		$dataset["stop"]						= time();
  		$objects["dataset"]					= $dataset;


  		print( Date("His:\n"));
      print( "\tLines read:\t$linecounter\n" );
      print( "\tEvents read:\t$eventcounter\n" );
      print( "\tViews read:\t" . ( $viewcounter ) . "\n" );
  		print( "\tSkipped:\t$eventsskipped\n" );

      var_dump( $dataset );
  }


  function signalhandler( $signal ){
      switch( $signal ) {
         
  			  case SIGTERM:
  	        print( "Received shutdown signal, shutting down now...\n" );
  	        flushdata();
  	        exit;
         
  			  case SIGINT:
  	        print( "Interrupted, quitting...\n" );
  	        flushdata();
  	        exit;  
      }
  }


  function flushdata(){

  		global $objects, $events, $fileprefix, $outputdir, $fp;
  		fclose($fp);
      printreport();
    
      
  		if( count( $objects ) > 0 ){ 
  	  	echo "\nflushing views to ./views-log.json ...";
      	if( file_put_contents("$outputdir/$fileprefix.views-log.json", json_encode( $objects )))
          echo "done!\n";
      	else
          echo "error!  Unable to flush data to file.\n";
    		}
  	
  		if( count( $events ) > 0 ){ 
  	  	echo "\nflushing events to ./events-log.json ...";
      
      	if( file_put_contents( "$outputdir/$fileprefix.events-log.json", json_encode( $events )))
          echo "done!\n";
      	else
          echo "error!  Unable to flush data to file.\n";
   			}
     }



  /**   MAIN   **/

  $fp = fopen( $logfile, "w");


  while( true ){

      $line = fgets( STDIN );
  		if(($linecounter & 3) === 3){
  			if(time()>$stoptime){
  				print("we have run our designated time of $timetorun seconds, clean up and exit\n");
  				flushdata();
  				die(0);
  				}
  		}
  //		fwrite($fp, $line);
  		//var_dump($logline);
  		$linecounter++;


      if (( strpos( $line, "SessionOpen", 6 )) === 6 ) {
          list( $ip, $counter, $port ) = explode( " ", substr( $line, 21 ));
          if( $skipevent ){ 
          	$eventsskipped++;
          }
          $skipevent = false;
      }
      
  		elseif ( strpos( $line, "RxURL", 6) === 6 ) {
          $urlarray = explode( "/", substr( $line, 22 ));
          if( count( $urlarray ) < 5 ){
          	print("Skipping event: $line\n");
  					$skipevent = true;
  	        continue;
  	        }
  				else{
  					} 
  	    
  				$elements 				= count( $urlarray ) >> 1;
  	    	$currentobjectid  = $urlarray[1];
  	    	$currenteventtype = $urlarray[0];
  	  
  			  unset( $params ); // Just-in-Time
  	
  	    	while( $elements-- > 1 ){ // 1 => skip the two first elements, they are objectid and eventtype
  	        $params[$urlarray[$elements << 1]] = $urlarray[( $elements << 1 ) + 1];  // create assoc
   	        }
  				unset($params['cb']);  // cachebuster param
  				unset($params['s']);	// 
          }
      
  		elseif ( strpos( $line, "ReqEnd", 6) == 6 ) {
      	if( $skipevent ){
         	print("Skipping line: $line\n");
  				continue;
  	  		}

      	$timeline = substr( $line, 21 );
      	list( $XID, $startproc, $endproc, $reqtime, $resptime, $deliverytime ) = explode( " ", $timeline );
      	$paramvalues = "";
       
      	if( !isset( $objects[$currenteventtype][$currentobjectid] )){ 

  	    	//no views/events yet on this objectid
        	$objects[$currenteventtype][$currentobjectid]['count'] = 1;
  				if( $currenteventtype != "v" ){
  						if($currenteventtype=="e"){
  						$eventcounter++;
  						$params['ip'] 	= $ip;
  						$params['time'] = $startproc;
  						$params['objectid'] = $currentobjectid;
  						
  						// add to events array.
  						$events[] = $params;
  //						unset( $params['objectid'] );
  						}
  					else{
  						print("Invalid eventtype: $currenteventtype\nurl: $line\n");
  						continue;
  						}
  						
  					}
  				else{
  					$viewcounter++;
  					}
  	    	foreach( $params as $key => $value ){
  	    		$objects[$currenteventtype][$currentobjectid][$key][$value]=1;
    	    	}
  	    	}
      	else {

  	    	// Count it
  	    	$objects[$currenteventtype][$currentobjectid]['count']++;
  				if( $currenteventtype != "v" ){
  					if($currenteventtype=="e"){
  						$eventcounter++;
  						$params['ip'] 	= $ip;
  						$params['time'] = $startproc;
  						$params['objectid'] = $currentobjectid;
  						
  						// add to events array.
  						$events[] = $params;
  						}
  				else{
  						print("Invalid eventtype: $currenteventtype\nurl: $line\n");
  						continue;
  						}
  					unset( $params['objectid'] );
  										
  					}
  				else{
  					$viewcounter++;
  					//print ( "counting: $line\n" );
  					}
  	    	foreach( $params as $key => $value ){
        		if( isset( $objects[$currenteventtype][$currentobjectid][$key][$value] )){
  	        	//Exists, so inc counter
  	        	$objects[$currenteventtype][$currentobjectid][$key][$value]++;
  	        	}
  	    		else{
  	        	// First of its kind
  	        	$objects[$currenteventtype][$currentobjectid][$key][$value] = 1;
    	      	}
          	}
      		}

  		//update screen

      if( DEBUG ){ // every x event

  			$memusage 			= memory_get_usage();
        $gccycles				= gc_collect_cycles(); // force garbage collection
  			$timecomponents = explode( ".", $startproc );
  			if( count( $timecomponents ) > 1 ){
  				$time  = strftime( "%T", (int) $timecomponents[0] ) . "." . substr( $timecomponents[1], 0, 3 );
  				}
        echo "\r$time\tviews:".number_format( $viewcounter ) . "\tEvents:\t" . 
  			number_format( $eventcounter ) . "\tmem:\t$memusage\t$ip $currenteventtype:$currentobjectid". 
  			( DEBUG ? "\n" : "" );
        }

      unset( $currentobjectid  );  
      unset( $currenteventtype );
      }
  }


  exit(0);

?>