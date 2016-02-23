<?php

echo  "respond.php : receive start<br>" ;
$fileContents = file_get_contents("php://input"); //接收post数据
$fileContents = str_replace(array("\n", "\r", "\t"), '', $fileContents);
$fileContents = trim(str_replace('"', "'", $fileContents));
$simpleXml = simplexml_load_string($fileContents);//转换post数据为simplexml对象
echo "[<br>";
foreach($simpleXml->children() as $child)    //遍历所有节点数据
{
	echo $child->getName() . ": " . $child . "<br>"; //打印节点名称和节点值
}
echo "]<br>";
$json = json_encode($simpleXml);
echo "Jason: $json <br>";
echo  "respond.php : receive end<br>" ;

exit;
?>
