
<?php
// Do a POST
$data= "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?> 
<sms-Request> 
<celular>Numero Celular</celular> 
<mensaje>Texto del Mensaje</mensaje> 
<numero_corto>numero corto</numero_corto> 
<carrier>Carrier Operadora MOVIL</carrier> 
</sms-Request>";
//echo "post data : $data <br>";

// create a new curl resource
$ch = curl_init();
// set URL and other appropriate options
//$post_ip = $_SERVER['SERVER_ADDR'];
$post_ip = "localhost";
curl_setopt($ch, CURLOPT_URL,"http://$post_ip/ant/sms.php");
//curl_setopt($ch, CURLOPT_URL,"http://$post_ip/curl_test/handle_form.php");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_HEADER,0);
// grab URL, and print
$content = curl_exec($ch);

echo "====================<br>";
var_dump($content);
echo "====================<br>";

