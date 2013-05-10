<?php

//Detect special conditions devices
$iPod = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
$iPhone = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
$iPad = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
$Android= stripos($_SERVER['HTTP_USER_AGENT'],"Android");
$webOS= stripos($_SERVER['HTTP_USER_AGENT'],"webOS");
$IntelMac= stripos($_SERVER['HTTP_USER_AGENT'],"Intel Mac");

//do something with this information
if( $iPod || $iPhone ){
        //were an iPhone/iPod touch -- do something here
		echo "Woohoo - on a iPhone or iPod";
}else if($iPad){
        //were an iPad -- do something here
		echo "Woohoo - on a iPad";
}else if($Android){
        //were an Android device -- do something here
		echo "Woohoo - on a Android";
}else if($webOS){
        //were a webOS device -- do something here
		echo "Woohoo - on a Computer";
}else if($IntelMac){
        //were a webOS device -- do something here
		echo "Woohoo - on a Intel Mac";
}
echo "<br>-ok-<br><br><br>";
echo $_SERVER['HTTP_USER_AGENT'];

?> 