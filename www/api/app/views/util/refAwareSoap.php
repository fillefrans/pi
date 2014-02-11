<?php

 /**
  *  @author Tomas Rundkaas <Tomas.Rundkaas@bayonette.no>
  *   
  *   - adapted to receive proper reply from .Net WebService "DirektInfo"
  */

class RefAwareSoap extends SoapClient{ 

  public function __doRequest($request, $location, $action, $version,$one_way = 0){ 

    try{
      $response = parent::__doRequest($request, $location, $action, $version); 
      $xml = simplexml_load_string($response); 

      if(false===(method_exists($xml, 'registerXPathNamespace'))){
        throw new Exception ("xml object has no method 'registerXPathNamespace': " + print_r($xml, true));
        return "";
      }

      $xml->registerXPathNamespace('z', 'http://schemas.microsoft.com/2003/10/Serialization/');
      foreach ($xml->xpath('//@z:Id') as $item) {
        //get ref key
        $varName = (string)$item[0];
         //get ref value
        $varValueTmp =$xml->xpath("//*[@z:Id='".$varName."']");
        $varValue =(string) ($varValueTmp[0]->Id);
        //replace the two
        $response = str_replace('z:Ref="'.$varName.'"', 'z:Id="'.$varValue.'"', $response); 
      }
    }     
  catch(Exception $e){
    // re-raise exception
    throw $e;    
    }
  return $response; 
  } 

} 


?>
