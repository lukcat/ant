<?php

$simpleXml;

$fileContents;

if (isset($HTTP_RAW_POST_DATA))
{
	$fileContents = $HTTP_RAW_POST_DATA;
}
else
{
	$fileContents = file_get_contents("php://input");
}

$simpleXml = simplexml_load_string($fileContents);

// write log
$file = fopen('smslog.txt','w');
$text = $simpleXml;
fwrite($file, $text);
fclose($file);

$respond = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\"?> 
<sms-Response>
<stauts>0</status>
<mensaje>SMS Recibido</mensaje>
</sms-Response>";


$ch = curl_init();

// A post url which mplus provide
$post_ip = "http://200.125.146.59:9042/mpluspru/mt?id=mplus&pwd=agsdfv67";
curl_setopt($ch, CURLOPT_URL, $post_ip);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $respond);
// grab URL, and print
curl_exec($ch);

