<?php

// Do a POST
$data = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\" ?> 
<sms-Request> 
<celular>0988748599</celular> 
<mensaje>Texto del Mensaje 2016-01-21.</mensaje> 
<servicio>14<rvicio> 
<id_mensaje>2533</id_mensaje> 
</sms-Request>";


// create a new curl resource
$ch = curl_init();
// set URL and other appropriate options
//$post_ip = $_SERVER['SERVER_ADDR'];
curl_setopt($ch, CURLOPT_URL,"http://200.125.146.59:9042/mpluspru/mt?id=mplus&pwd=agsdfv67");

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
// grab URL, and print
curl_exec($ch);


