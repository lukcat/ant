
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
foreach($simpleXml->children() as $child)   
{
	echo $child->getName() . ": " . $child . "<br>"; 
}

$json = json_encode($simpleXml);
// process json here

$respond = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\" ?> 
<sms-Response> 
<status>0</status> 
<mensaje>SMS Recibido</mensaje> 
</sms-Response>";

// create a new curl resource
$ch = curl_init();
// set URL and other appropriate options
$post_ip = $_SERVER['SERVER_ADDR'];
curl_setopt($ch, CURLOPT_URL,"http://$post_ip/curl_test/respond.php");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $respond);
// grab URL, and print
curl_exec($ch);
echo  "handle_from.php: send respond<br>" ;

?>

