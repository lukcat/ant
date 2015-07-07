<?php
//echo phpinfo();
//echo empty($test);

$i = 0;
$sum = 3;

$resData = array();

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

print_r($resData);
