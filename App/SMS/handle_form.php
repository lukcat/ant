
<?php

echo  "handle_from.php : receive start<br>" ;
$fileContents = file_get_contents("php://input"); //接收post数据
$fileContents = str_replace(array("\n", "\r", "\t"), '', $fileContents);
$fileContents = trim(str_replace('"', "'", $fileContents));
$simpleXml = simplexml_load_string($fileContents);
echo "[<br>";
foreach($simpleXml->children() as $child)    //遍历所有节点数据
{
	echo $child->getName() . ": " . $child . "<br>"; //打印节点名称和节点值
}
echo "]<br>";

$json = json_encode($simpleXml);
echo "Jason: $json <br>";
echo "handle_from.php : receive end<br>";

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

