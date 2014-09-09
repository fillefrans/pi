    <?php

    /**
     * Run batch through Pi WebService
     *
     * @author Johan Telstad, jt@viewshq.no
     * @version v1.0, 04.02.2013
     *
     *  Usage example at the bottom of this file
     */


    define('PI_ROOT', '/home/kroma/dev/www/pi/srv/php/');
    require_once PI_ROOT."pi.config.php";
    require_once PI_ROOT."pi.exception.class.php";
    require_once UTILITIES_DIR."pi.functions.php";


    /**
     *  Catch unhandled exceptions
     *
     * @param Exception $exception
     */
    function exception_handler(Exception $exception) {
      die(json_encode(array('OK'=>0, 'message'=>'Unhandled '. get_class($exception) .": ". $exception->getMessage())));
    }

    set_exception_handler('exception_handler');

    /**
     *  Class WebServiceBatch
     *
     *  @param String   serviceUrl  URL of WebService
     *  @param String   input       Inputfile in CSV format with number as the first field
     *  @param String   method      Valid inputs are "curl" or "fsock", default is curl
     *
     *  @throws Exception   If Param input is not an existing file, or file format is invalid.
     *  @throws Exception   If Param serviceUrl is not a valid URL, or if an error occurs when calling WebService
     *  
     */

    class WebServiceBatch {

        protected $columns    = array();
        protected $timer      = array();
        protected $report     = array();
        protected $log        = array();
        protected $webService = array( 'job'=>3, 'apikey'=>'kroma', 'client'=>2 );
        protected $serviceUrl = null;
        protected $inputfile  = null;
        protected $number     = null;
        protected $error      = null;
        protected $method     = null;
        protected $limit      = 0;
        protected $multiexec  = 0;


        public function __construct( $serviceurl = null, $inputfileOrSingleNumber = null, $method = "curl" ) {
          $this->serviceUrl             = $serviceurl;
          $this->method                 = strtolower($method);
          $this->timer["transactions"]  = array();

          if (file_exists($inputfileOrSingleNumber)) {
            $this->inputfile = $inputfileOrSingleNumber;
          }
          else {
            $this->error = "File does not exist: $inputfileOrSingleNumber.";
            throw new Exception("Invalid parameter: $inputfileOrSingleNumber\n" . 
                                "File does not exist: $inputfileOrSingleNumber.");
          }
        }


        protected function readInputFile( $inputfile ){
          if(!file_exists($inputfile)){
            throw new Exception("Input file does not exist: $inputfile");
          }
          $linecounter = 0;
          $fp = fopen( $inputfile, "r" );
          while(!feof($fp)){
            if($this->limit>0){
              if($linecounter >= $this->limit){
                $this->say("Reached our limit of {$this->limit} lines processed.");
                $this->say("Header row: " . print_r($this->columns, true));
                break;
              }
            }
            $csvarray = fgetcsv($fp, 0, ';');
            if(0===$linecounter++){
              if(!is_numeric($csvarray[0])){

                // we have column headers in line 1
                $this->columns = $csvarray;
                continue;
              }
              else{
                $this->columns = array_fill(0, count($csvarray), "");
              }
            }

            if(count($csvarray)>0){
              print_r($csvarray);
              continue;
              $this->callWebService($csvarray[0]);
            }
          }
          fclose($fp);
        }


        protected function say( $value = "" ) {
          $this->log[] = $value;
          print($value);
        }


        protected function callWebService($number) {

          if( is_numeric(str_replace(" ", "", $number)) ) {
            $this->number = str_replace(" ", "", $number);
          }
          else{
            throw new Exception("Not a number: $number.");
          }

          if( $this->method === "curl" ){
            $this->callWebServiceCurl($this->number);
          }
          elseif ( $this->method === "fsock" ) {  
            $this->callWebServiceFsock($this->number);
          }
          else{
            throw new Exception("Invalid method: " . $this->method);
          }
        }


        /**
         *  call WebService using fsock
         */

        protected function callWebServiceFsock( $number="" ) {
          $this->webService['phone'] = $number;
          $json     = json_encode($this->webService);
          $jsonsize = strlen($json);
          $result   = true;
     
          //open a connection to server
          $connection = fsockopen( 'www.Pi.no', 80 );

          try{
            $POSTDATA   = array();
            $POSTDATA[] = "POST /app/webservice/job/index.php HTTP/1.1\r\n";
            $POSTDATA[] = "Host: www.Pi.no \r\n";
            $POSTDATA[] = "Content-Type: application/json \r\n";
            $POSTDATA[] = "Content-Length: $jsonsize\r\n";
            $POSTDATA[] = "Connection: close\r\n\r\n";
            $POSTDATA[] = $json;
            $answer     = array();

            $this->say("Posting to server:\n");

            //sending the data
            foreach ($POSTDATA as $line) {
              fputs($connection, $line);
              $this->say($line);
            }

            // flush data to server
            fflush($connection);

            $this->say("\nServer says:\n");

            while (!feof($connection)) {
              $line = fgets($connection);
              $answer[] = $line;
              $this->say( $line . "\n" );
            }
          }
          catch( Exception $e ){
            $this->say( "ERROR: ". get_class($e) . " - " . $e->getMessage() ."\n" );
            throw $e;
          }

          //close the connection
          fclose($connection);
          return $result;
        }


        protected function callWebServiceCurlMulti($numbers = null){
          if(!is_array($numbers)){
            throw new Exception("callWebServiceCurlMulti: Expecting array, got " . get_class($numbers));
          }
          $curls = array();
          $multicurl = curl_multi_init();

          for($i = 0; $i < count($numbers); $i++) {
            $curls[$i] = curl_init($url);
            $this->webService['phone'] = $numbers[$i];

            // the data we will post
            $json = json_encode($this->webService);

            $datalength   = strlen($json);

            curl_setopt( $curls[$i], CURLOPT_RETURNTRANSFER, true);
            curl_setopt( $curls[$i], CURLOPT_URL,           "http://www.Pi.no/app/webservice/job/" );
            curl_setopt( $curls[$i], CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $curls[$i], CURLOPT_CUSTOMREQUEST,  "POST" );    
            curl_setopt( $curls[$i], CURLOPT_CONNECTTIMEOUT, 30 );
            curl_setopt( $curls[$i], CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $curls[$i], CURLOPT_FOLLOWLOCATION, 1 );
            curl_setopt( $curls[$i], CURLOPT_POSTFIELDS,     $json );
            curl_setopt( $curls[$i], CURLOPT_HTTPHEADER,     array(
                        "Content-Type: application/json",                                                                                
                        "Content-Length: $datalength" ));
            curl_multi_add_handle($multicurl, $curls[$i]);
          }

          do {
            curl_multi_exec($multicurl,$running);
          } while($running > 0);


          for($i = 0; $i < count($curls); $i++) {
            $results[] = curl_multi_getcontent  ( $curls[$i]  );
          }
          var_dump($results);
        }


        /**
         *  Call WebService using cURL
         */

        protected function callWebServiceCurl( $number=null ) {
          $this->webService['phone'] = $number;

          // the data we will post
          $json = json_encode($this->webService);

          $datalength   = strlen($json);
          $POSTDATA     = $json;
          $curlresult   = "";
          $resultinfo   = "";      
          $result       = true;

          $curl = curl_init();

          try{

            //set options
            curl_setopt( $curl, CURLOPT_URL, "http://www.Pi.no/app/webservice/job/" );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $curl, CURLOPT_CUSTOMREQUEST,  "POST" );    
            curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 30 );
            curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, 1 );
            curl_setopt( $curl, CURLOPT_POSTFIELDS,     $json );
            curl_setopt( $curl, CURLOPT_HTTPHEADER,     array(
                        "Content-Type: application/json",                                                                                
                        "Content-Length: $datalength" ));
            
            //perform request
            $curlresult = curl_exec($curl);

            //show information regarding the request
            $this->say( print_r(curl_getinfo($curl), true) . "\n" );
            if($curlerrno = ( curl_errno($curl) )) {
              $this->say( "CURL ERROR: " . $curlerrno . " - " . curl_error($curl) . "\n" );
              $result = false;
            }

            $this->say($curlresult);
            $result = true;
            curl_close($curl);
          }
          //close the connection 
          catch( Exception $e ) {
            $result = false;
            curl_close($curl);
          }
          return $result;
        }


      public function run($limit=0, $multiexec=0) {
        $this->limit = $limit;
        $this->multiexec = $multiexec;
        if($multiexec!==0){
          if($this->method!=="curl") {
            throw new Exception(get_class($this) . "->run(): " . "Parallel WS-requests (multiexec) is only available with method 'curl'.");
          }
        }
        if(!is_null($this->inputfile)) {
          $this->readInputFile($this->inputfile);
        }
        elseif(!is_null($this->number)) {
          $this->callWebService($this->number);
        }
        else{
          throw new Exception(get_class($this) . "->run(): " . "No input file to process.");
        }
      }
    }


    /**
     *  Usage example
     * 
     */

    $service  = "http://www.Pi.no/app/webservice/job/";
    $method   = "curl";
    $file     = "/home/kroma/dev/www/pi/srv/php//php/nrk.kbatch.csv";


    try {
      $webservice = new WebServiceBatch( $service, $file, $method );
      $webservice->run(10);
    }
    catch( Exception $e ) {
      print( "ERROR: " . get_class($e) . " - " . $e->getMessage() );
    }

?>