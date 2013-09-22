<?php

  if(!defined('CONFIG_ROOT')){
    define('CONFIG_ROOT', '/home/kroma/scripts/views/php/server/config/');  
  }
  require_once(CONFIG_ROOT."views.config.php");

require_once(UTILITIES_DIR."refAwareSoap.php");
include_once(UTILITIES_DIR."json-pretty-print.php");

$result = array();


$rowheaders   = array('idx','type', 
  'direktinfo_id', 'zipCode', 'county', 'state', 'lat', 'lon', 'community', 'residentialType', 'age', 'lifePhase', 'sex', 'isDeceased', 'isDMReserved', 
  'isTMReserved', 'isHMReserved', 'CategoryConsumerStrength', 'CategoryLifephase', 'CategoryUrban', 'EstimatedLengthOfEducation', 'FISEducation', 
  'FISIncome', 'FISWealth', 'HouseholdEstimatedIncome', 'HouseholdEstimatedMembersCount', 'HouseholdEstimatedWealth', 'LifePhaseProfile',
  'PersonEstimatedIncome', 'PersonEstimatedWealth');


function encode_result($row){
	global $request, $debug, $rowheaders;

	if(isset($request['packedNumber'])){
		$res['idx'] = sha1($request['packedNumber']);
	}
	else{
		$res['idx'] = sha1($request['phone']);
	}
	$res['type'] = $row['type'];
	switch($row['type']){
		case "person": 
				foreach ($row['info'] as $key => $value) {
					if($key==='direktinfo'){
						$res['direktinfo_id'] = $value['id'];
					}
					else{
						if(is_array($value)){
							foreach ($value as $subkey => $subvalue) {
								if(in_array($subkey, $rowheaders)){
									$res[$subkey] = $subvalue;
								}
							}
						}
						else{
							if(in_array($key, $rowheaders)){
								$res[$key] = $value;
							}
						}
					}
				}
				break;

		case "company": 
				$res['direktinfo_id']	= $row['info']['direktinfo']['id'];
				$res['zipCode'] 			= $row['info']['zipCode'];
				$res['county'] 				= $row['info']['county'];
				$res['state'] 				= $row['info']['state'];
				break;

	}//case

	/*
	// probably superfluous

	while (count($res)<30) {
		$res[]='NULL';
	}
	*/
	return $res;
}


/*
OLD version of function encode_result
function encode_result($row){
	global $request;
	if(DEBUG){
		$res[] = $request['phone'];
	}
	else{
		$res[] = sha1($request['phone']);
	}
	$res[] = $row['type'];
	switch($row['type']){
		case "person": 
				$res[] = $row['info']['direktinfo']['id'];
				$res[] = $row['info']['zipCode'];
				$res[] = $row['info']['county'];
				$res[] = $row['info']['state'];
				$res[] = $row['info']['lat'];
				$res[] = $row['info']['lon'];
				$res[] = $row['info']['community'];
				$res[] = $row['info']['residentialType'];
				$res[] = $row['info']['age'];
				$res[] = $row['info']['lifePhase'];
				$res[] = $row['info']['sex'];
				$res[] = $row['info']['isDeceased'];
				$res[] = $row['info']['isDMReserved'];
				$res[] = $row['info']['isTMReserved'];
				$res[] = $row['info']['isHMReserved'];
				if(isset($row['details'])){
					$res[] = $row['info']['details']['CategoryConsumerStrength'];
					$res[] = $row['info']['details']['CategoryLifephase'];
					$res[] = $row['info']['details']['CategoryUrban'];
					$res[] = $row['info']['details']['EstimatedLengthOfEducation'];
					$res[] = $row['info']['details']['FISEducation'];
					$res[] = $row['info']['details']['FISIncome'];
					$res[] = $row['info']['details']['FISWealth'];
					$res[] = $row['info']['details']['HouseholdEstimatedIncome'];
					$res[] = $row['info']['details']['HouseholdEstimatedMembersCount'];
					$res[] = $row['info']['details']['HouseholdEstimatedWealth'];
					$res[] = $row['info']['details']['LifePhaseProfile'];
					$res[] = $row['info']['details']['PersonEstimatedIncome'];
					$res[] = $row['info']['details']['PersonEstimatedWealth'];
				}
				break;

		case "company": 
				$res[] = $row['info']['direktinfo']['id'];
				$res[] = $row['info']['zipCode'];
				$res[] = $row['info']['county'];
				$res[] = $row['info']['state'];
				break;
	}//case

	// probably superfluous, this while loop

	while (count($res)<30) {
		$res[]='NULL';
	}
	return $res;
}


*/

/*
	WEB SERVICE COMM. FUNCTIONS BELOW

*/


function getInfoFromWebService($phone, $extended=false){
	$result['type']= 'person';

	$apiKey= "49NfKV5WKvrhp2iFUti4RfLHnC61i0JquLBTAXCf36A=";//your API key here
	$client = new SoapClient('http://www.direktinfo.no/service/interopt/SearchService.svc.wsdl', array(' features' => SOAP_SINGLE_ELEMENT_ARRAYS));
	
	$personSearchParameters = array("apiKey"=>$apiKey, "criteria"=>$phone, "tag"=>"", "pageIndex"=>"0", "pageSize"=>"1"); 
	$companySearchParameters = array("apiKey"=>$apiKey, "criteria"=>"$phone", "tag"=>"", "pageIndex"=>"0", "pageSize"=>"1"); 
	
	$person = $client->SearchPaged($personSearchParameters);

	$personcount = $person->SearchPagedResult->PersonCount;

	if($personcount > 0 ){	
		if(isset($person->SearchPagedResult->Persons->Person)){
			if(!is_object($person->SearchPagedResult->Persons->Person)){
				//print ("    person from WS : " . print_r($person, true));
				$result['type'] = "unknown";
				}
			else{
				$personinfo = parsePersonResult( $person->SearchPagedResult->Persons->Person );
				$result['info']=$personinfo;	
				if($extended && isset($result['info']['direktinfo']['id'])){
					// get extended info
					$result['info']['details'] = getDetailedInfoFromWebService($personinfo['direktinfo']['id']);
					}
				else{
					//updateStatus(" -> direktinfo id not set : " . print_r($result, true));
					}
				}
			}
		} //if personcount > 0
	else {
		$companycount 	= $person->SearchPagedResult->CompanyCount;	
		//updateStatus("\n    CompanyCount : " . $companycount);
		if($companycount > 0 ){
			$result['type']= 'company';
			//parse first result in resultset
			$companyinfo = parseCompanyResult( $person->SearchPagedResult->Companies->Company );
			$result['info']=$companyinfo;	
			//updateStatus("\n    company, ". print_r($companyinfo, true) ."");
			}
		else{
			$result['type']= 'unknown';
			//updateStatus(" -> unknown");
		}	
	} // end else (if personcount > 0)
 return $result;
}


function getDetailedInfoFromWebService($id){
	$result = FALSE;
//	updateStatus("\nPerson $id : ");
	$apiKey= "49NfKV5WKvrhp2iFUti4RfLHnC61i0JquLBTAXCf36A=";//your API key here

  $client = new RefAwareSoap('http://www.direktinfo.no/service/interopt/SearchService.svc.wsdl',array('trace' => true, 'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP));

	$companyLookupParameters = array("apiKey"=>$apiKey, "productCode"=>"109", "tag"=>"Detail page", "orgNumber" => array("992224118"));
	$personLookupParameters = array("apiKey"=>$apiKey, "productCode"=>"109", "tag"=>"Detail page", "personId" => array($id),"extended" => true);
	
	$person = $client->ConsumerLookup($personLookupParameters); 
	if(isset($person->ConsumerLookupResult->Persons->Person->PersonDemographics->PersonDemographics)){
		$result = $person->ConsumerLookupResult->Persons->Person->PersonDemographics->PersonDemographics;		
/*
                "Id":"73b17183-9914-de11-a779-00e0812f8b7c",
                "CategoryConsumerStrength":"02. Middels",
                "CategoryLifephase":"03. Par uten barn",
                "CategoryUrban":"02. Tettsted",
                "EstimatedLengthOfEducation":null,
                "FISEducation":null,
                "FISIncome":"04. 250-300",
                "FISWealth":"01. 0",
                "HouseholdEstimatedIncome":428428,
                "HouseholdEstimatedMembersCount":2,
                "HouseholdEstimatedTax":156092,
                "HouseholdEstimatedWealth":530634,
                "LifePhaseProfile":"23_L3_T_M",
                "PersonEstimatedIncome":258283,
                "PersonEstimatedTax":97829,
                "PersonEstimatedWealth":0,
                "ProbabilityChildren0005":"41.9",
                "ProbabilityChildren0617":"6.8"

*/

		unset($result->Id);
//		unset($result->HouseholdEstimatedIncome);
		unset($result->HouseholdEstimatedTax);
//		unset($result->HouseholdEstimatedWealth);
//		unset($result->HouseholdEstimatedIncome);
//		unset($result->PersonEstimatedIncome);
		unset($result->PersonEstimatedTax);
//		unset($result->PersonEstimatedWealth);
		unset($result->ProbabilityChildren0005);
		unset($result->ProbabilityChildren0617);
		
		if($result->CategoryLifephase){
			$result->CategoryLifephase = substr($result->CategoryLifephase, 0, 2);
			}
		if($result->CategoryUrban){
			$result->CategoryUrban = substr($result->CategoryUrban, 0, 2);
			}
		if($result->CategoryConsumerStrength){
			$result->CategoryConsumerStrength = substr($result->CategoryConsumerStrength, 0, 2);
			}
		if($result->FISIncome){
			$result->FISIncome = substr($result->FISIncome, 0, 2);
			}
	
		if($result->FISEducation){
			$result->FISEducation = substr($result->FISEducation, 0, 2);
			}
		if($result->FISWealth){
			$result->FISWealth = substr($result->FISWealth, 0, 2);
			}
		}
	else{
//		updateStatus(" -> PersonDemographics not set!");
	}
 	return $result;
}


function parseAddress( $addressresult ){
	global $keyValues;
	$addressinfo = NULL;
	if(gettype($addressresult) == gettype(""))
		return FALSE;

	$addressSize = strlen(serialize($addressresult));

	// it is not a full address, but a fragment of some kind
	if($addressSize<400){
		print ("    SizeOf (address) = $addressSize");
		print_r($addressresult);
		return FALSE;
	}
	

	if(isset($addressresult->AddressMember->PostalCityMember->Id)){
		$addressinfo['zipCode']	= $addressresult->AddressMember->PostalCityMember->Id;
				
		$addressinfo['county'] = $addressresult->AddressMember->PostalCityMember->CountyMember->Id;

		$addressinfo['state'] = $addressresult->AddressMember->PostalCityMember->CountyMember->StateMember->Id;

		if(isset($addressresult->AddressMember->PostalCityMember->AreaMember->Id)){
			$addressinfo['area'] = $addressresult->AddressMember->PostalCityMember->AreaMember->Id;
			}

		}
	else{
		return FALSE;
	}

	if(isset($addressresult->AddressMember->Latitude)){
		$addressinfo['lat'] = $addressresult->AddressMember->Latitude;
		$addressinfo['lon'] = $addressresult->AddressMember->Longitude;
	}

	if(isset($addressresult->AddressMember->BasicStatisticalUnitMember->Id)){
		$addressinfo['community'] = $addressresult->AddressMember->BasicStatisticalUnitMember->Id;
		}

	if(isset($addressresult->PersonMember->Ref)){
		$addressinfo['ref'] = $addressresult->PersonMember->Ref;
	}
	if(isset($addressresult->ResidentialTypeMember->Id)){
		$addressinfo['residentialType'] = $addressresult->ResidentialTypeMember->Id;
	}

	return $addressinfo;
	}


function parseCompanyAddress( $addressresult ){
	$addressinfo = NULL;
	if(gettype($addressresult) == gettype(""))
		return FALSE;


	$addressSize = strlen(serialize($addressresult));

	// it is not a full address, but a fragment of some kind
	if($addressSize<400){
		print ("    SizeOf (companyaddress) = $addressSize");
		print_r($addressresult);
		return FALSE;
	}


	if(isset($addressresult->PostalCityMember->Id)){
		//updateStatus("\ncompany postnr: " . $addressresult->PostalCityMember->Id . "");
		// store zipCode, county number, state number (names will be retrieved otherwhere)
		$addressinfo['zipCode']	= $addressresult->PostalCityMember->Id;
		$addressinfo['county'] 	= $addressresult->PostalCityMember->CountyMember->Id;
		$addressinfo['state'] 	= $addressresult->PostalCityMember->CountyMember->StateMember->Id;
		}
	else{
		//updateStatus("\nZipCode not set ! typeof(addressresult) = " . gettype($addressresult)."");
		return FALSE;
	}

	if(isset($addressresult->Latitude)){
		$addressinfo['lat'] = $addressresult->Latitude;
		$addressinfo['lon'] = $addressresult->Longitude;
	}

	if(isset($addressresult->BasicStatisticalUnitMember->Id)){
		$addressinfo['community'] = $addressresult->BasicStatisticalUnitMember->Id;
	}

	return $addressinfo;
	}


function parseCompanyResult($companyresult){
	$companyinfo=array("direktinfo"=>array("id"=>$companyresult->Id));

	$companyinfo['direktinfo']['id'] = $companyresult->Id;

	if(is_array($companyresult->CompanyAddresses->Address)){
		$doparse = $companyresult->CompanyAddresses->Address[0];
	}
	else{ 
		$doparse = $companyresult->CompanyAddresses->Address;
		} 
	if(FALSE !== ($thisaddress=parseCompanyAddress($doparse))){
		if($thisaddress != NULL){
			foreach ($thisaddress as $key => $value) {
				$companyinfo[$key] = $value;	
				}
			}
		}
	else{
		//updateStatus("\nNo address found for company!");
		}

	$companyinfo['active'] 			= $companyresult->Active;
	$companyinfo['employees']		= $companyresult->NumberOfEmployees;
	$companyinfo['marketName'] 		= $companyresult->MarketName;
	$companyinfo['name'] 			= $companyresult->Name;
	$companyinfo['orgNo'] 			= $companyresult->OrganizationNumber;
	$companyinfo['creditRating'] 	= $companyresult->CreditRating;

	if(isset($companyresult->CompanyEntityTypeMember->Id))
		$companyinfo['entityType'] 	= $companyresult->CompanyEntityTypeMember->Id;

	if(isset($companyresult->CompanyClassifications->CompanyClassification->NaceMember->Id)){
		$companyinfo['NACE'] = $companyresult->CompanyClassifications->CompanyClassification->NaceMember->Id;
		$companyinfo['isPublicSector'] = $companyresult->CompanyClassifications->CompanyClassification->NaceMember->IsPublicSector;
		$companyinfo['NACElevel'] = $companyresult->CompanyClassifications->CompanyClassification->NaceMember->Level;
	}
	elseif(isset($companyresult->CompanyClassifications->CompanyClassification[0]->Nacemember->Id)){
		$companyinfo['NACE'] = $companyresult->CompanyClassifications->CompanyClassification[0]->NaceMember->Id;
		$companyinfo['isPublicSector'] = $companyresult->CompanyClassifications->CompanyClassification[0]->NaceMember->IsPublicSector;
		$companyinfo['NACElevel'] = $companyresult->CompanyClassifications->CompanyClassification[0]->NaceMember->Level;
	}

	if(isset($companyresult->CompanyEndpoints->Endpoint)){
		if(is_array($companyresult->CompanyEndpoints->Endpoint)){
			foreach ($companyresult->CompanyEndpoints->Endpoint as $endpoint) {
				if(isset($endpoint->EndpointTypeMember->Id)){
					if($endpoint->EndpointTypeMember->Id == 5){
						$companyinfo['website'] = $endpoint->Value;
						}
					elseif($endpoint->EndpointTypeMember->Id == 4){
						$companyinfo['email'] = $endpoint->Value;
					}
					elseif($endpoint->IsMain==1){
						$companyinfo[strtolower($endpoint->EndpointTypeMember->Name)] = $endpoint->Value;
					}
				}
			}
		}
	} // if isset (endpoint...)

//	updateStatus("\ncompany result : " . print_r($companyresult,true));
	
	return $companyinfo;
	}


function parsePersonResult($personresult){
	$personinfo=array("direktinfo"=>array("id"=>$personresult->Id));

	// is there more than one address ?
	if(isset($personresult->PersonAddresses->PersonAddress)){
		if(is_array($personresult->PersonAddresses->PersonAddress)){
			// if so, then parse only the first address entry
			//updateStatus("\nNo. of addresses:".count($personresult->PersonAddresses->PersonAddress). "");
			$doparse = $personresult->PersonAddresses->PersonAddress[0];
			if(!$doparse){
				//die("doparse is not!" . print_r($personresult));
			}
		}
	else{ // there is only one address entry, so parse that one
		$doparse = $personresult->PersonAddresses->PersonAddress;
			if(!$doparse){
				die("doparse is not set!");
			}
		} 
	}
	if(isset($doparse)){
		if(FALSE !== ($thisaddress=parseAddress($doparse))){
			if($thisaddress != NULL){
				foreach ($thisaddress as $key => $value) {
					$personinfo[$key] = $value;	
					}
				}
			}
		else{
			//updateStatus("\nNo address found for person!");
			}
	}
	if(strtotime($personresult->BirthDate)!=0){
		$personinfo['age'] = floor( (time() - strtotime($personresult->BirthDate))/31536000 );
		}
	else{
		$personinfo['age'] = NULL;
	}
	if(isset($personresult->LifePhaseMember->Id)){
		$personinfo['lifePhase'] = ($personresult->LifePhaseMember->Id);
		}



	$personinfo['sex'] 					= $personresult->Gender;
	$personinfo['isDeceased'] 	= $personresult->IsDeceased;
	$personinfo['isDMReserved'] = $personresult->IsDMReserved;
	$personinfo['isTMReserved'] = $personresult->IsTMReserved;
	$personinfo['isHMReserved'] = $personresult->IsHMReserved;
	$personinfo['score'] 				= $personresult->Score;

	return $personinfo;
	}


?>
