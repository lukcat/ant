<?php
//echo phpinfo();
//echo empty($test);

/*
function test() {
$i = 0;
$sum = 3;

//$resData = array();

for(;$i < $sum; $i++) {
    $filename = "filename".$i;
    $code = $i;
    $msg = "this is ".$i. "message";

    $data = array(
            'code' => $code,
            'filename' => $filename,
            'msg' => $msg,
            );
    $resData[$filename] = $data;
}

return $resData;
}
$data = test();

print_r($data);
*/

//$arr1 = array('a','b','c','d');
//$arr2 = array();
//
//for ($i=0; $i<10; $i++) {
//    array_push($arr2, $arr1);
//}
//
//var_dump($arr2);

/*
$len = 4;
$min = 0;
$max = 9;
$securityCode = '';
for ($i=0; $i<$len; $i++) {
    $securityCode .= rand($min, $max);
}
echo $securityCode;
*/

//$sql = "SELECT CREATE_TIME FROM APP_USER WHERE LOGIN_NAME='cdqing'";
//
//$stts = oci_parse(


//$d = date('Ymd');
//
//mkdir($d);

//$str = 'test';
//echo mb_strlen($str);

// multiple process
//parentPid = getmypid();
//$pid = pcntl_fork();
//if ($pid == -1) {
//    die('fork failsed');
//} else if ($pid == 0) {
//    $myPid = getmypid();
//    echo 'I am child process. My Pid is '. $myPid . " and my father's Pid is " . $parentPid . PHP_EOL;
//} 
//echo "Oh my god! I am a father now! My child's PID is " . $pid . ' and my PId is ' . $parentPid . PHP_EOL;

function guid(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = //chr(123)// "{"
                substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);
                //.chr(125);// "}"
        return $uuid;
    }
}

echo 'guid is' . PHP_EOL;
echo guid();
