<?php


/*
    "auth_uri":"https://accounts.google.com/o/oauth2/auth",
    "client_secret":"lgTBW-yqPKAMuFCXHC3mKiji",
    "token_uri":"https://accounts.google.com/o/oauth2/token",
    "client_email":"232265062151-7nnv4q54iekjsluti2odg32ttvseh45j@developer.gserviceaccount.com",
    "redirect_uris":["http://www.kromaviews.no:8080/pi/app/auth/",
    "http://kromaviews.no:8080/pi/app/auth/",
    "http://www.kromaviews.no/pi/app/auth/",
    "http://kromaviews.no/pi/app/auth/",
    "https://kromaviews.no/pi/app/auth/"],
    "client_x509_cert_url":"https://www.googleapis.com/robot/v1/metadata/x509/232265062151-7nnv4q54iekjsluti2odg32ttvseh45j@developer.gserviceaccount.com",
    "client_id":"232265062151-7nnv4q54iekjsluti2odg32ttvseh45j.apps.googleusercontent.com",
    "auth_provider_x509_cert_url":"https://www.googleapis.com/oauth2/v1/certs",
    "javascript_origins":["http://kromaviews.no",
    "http://kromaviews.no:8080",
    "https://kromaviews.no",
    "http://www.kromaviews.no",
    "http://www.kromaviews.no:8080",
    "https://www.kromaviews.no"]

 */







  ########## Google Settings.. Client ID, Client Secret #############
  $google_client_id 		= '442957159084-a3u516hbflosamuac8meepspq04katn9.apps.googleusercontent.com';
  $google_client_secret = 'tCjIhxOPezZmDXxQoz1Hh_Xw';
  $google_redirect_url 	= 'http://kromaviews.no:8080/pi/app/auth/';
  $google_developer_key = 'AIzaSyApqMMcDRheAWtFSX5Ln937wupX9TUMp5E';

  ########## MySql details (Replace with yours) #############
  $db_username  = "pi";         //Database Username
  $db_password  = "3.141592";   //Database Password
  $hostname     = "localhost";  //Mysql Hostname
  $db_name      = 'pi';         //Database Name
  ###################################################################

  //include google api files
  require_once 'src/Google_Client.php';
  require_once 'src/contrib/Google_Oauth2Service.php';

  //start session
  session_start();

  $gClient = new Google_Client();
  $gClient->setApplicationName('Login to Pi');
  $gClient->setClientId($google_client_id);
  $gClient->setClientSecret($google_client_secret);
  $gClient->setRedirectUri($google_redirect_url);
  $gClient->setDeveloperKey($google_developer_key);

  $google_oauthV2 = new Google_Oauth2Service($gClient);

  //If user wish to log out, we just unset Session variable
  if (isset($_REQUEST['reset'])) {
    unset($_SESSION['token']);
    $gClient->revokeToken();
    header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
  }

  //Redirect user to google authentication page for code, if code is empty.
  //Code is required to aquire Access Token from google
  //Once we have access token, assign token to session variable
  //and we can redirect user back to page and login.
  if (isset($_GET['code'])) { 
  	$gClient->authenticate($_GET['code']);
  	$_SESSION['token'] = $gClient->getAccessToken();
  	header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
  	return;
  }


  if (isset($_SESSION['token'])) { 
  		$gClient->setAccessToken($_SESSION['token']);
  }


  if ($gClient->getAccessToken()) {
  	  //Get user details if user is logged in
  	  $user 				= $google_oauthV2->userinfo->get();
  	  $user_id 				= $user['id'];
  	  $user_name 			= filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
  	  $email 				= filter_var($user['email'], FILTER_SANITIZE_EMAIL);
  	  $profile_url 			= filter_var($user['link'], FILTER_VALIDATE_URL);
  	  $profile_image_url 	= filter_var($user['picture'], FILTER_VALIDATE_URL);
  	  $personMarkup 		= "$email<div><img src='$profile_image_url?sz=50'></div>";
  	  $_SESSION['token'] 	= $gClient->getAccessToken();
  }
  else {
  	//get google login url
  	$authUrl = $gClient->createAuthUrl();
  }

  //HTML page start
  echo '<html lang="no">';
  echo '<head>';
  echo '<meta charset=utf-8" />';
  echo '<title>Login with Google</title>';
  echo '</head>';
  echo '<body>';
  echo '<h1>Login with Google</h1>';

  if(isset($authUrl)) {
    //user is not logged in, show login button
  	echo '<a class="login" href="'.$authUrl.'">Google</a>';
    } 
  else {
    // user logged in 
    
    /* connect to mysql */
    $connecDB = mysql_connect($hostname, $db_username, $db_password)or die("Unable to connect to MySQL");
    mysql_select_db($db_name,$connecDB);
	
    //compare user id in our database
    $result = mysql_query("SELECT COUNT(google_id) FROM google_users WHERE google_id=$user_id");
  	if($result === false) { 
  		die(mysql_error()); //result is false show db error and exit.
  	}
  	
  	$UserCount = mysql_fetch_array($result);
   
    if($UserCount[0]) {
      //user id exist in database
  		echo 'Welcome back '.$user_name.'!';
      }else{ //user is new
  		echo 'Hi '.$user_name.', Thanks for Registering!';
  		@mysql_query("INSERT INTO google_users (google_id, google_name, google_email, google_link, google_picture_link) VALUES ($user_id, '$user_name','$email','$profile_url','$profile_image_url')");
  	}

	
  	echo '<br /><a href="'.$profile_url.'" target="_blank"><img src="'.$profile_image_url.'?sz=50" /></a>';
  	echo '<br /><a class="logout" href="?reset=1">Logout</a>';
  	
  	//list all user details
  	echo '<pre>'; 
  	print_r($user);
  	echo '</pre>';	
  }
   
  echo '</body></html>';
?>