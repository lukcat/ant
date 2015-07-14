<?php

// insert into app_user

// Coordinated Universal Time
date_default_timezone_set('UTC');

$username = 'ant';
$password = 'ant';
$server = '192.168.146.88/mobile';

// connect database
$conn = oci_connect($username,$password,$server);

if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

// test data
$userid = md5(uniqid(microtime(true),true));
$loginname = 'chendq';
$name = 'chendeqing';
$email = 'chendq@test.com';
$cellphone = '12345678901';
$note = 'lanren';
$valid = 1;
$password = sha1(md5('test'));
$create_t = date('Y-m-d H:i:s');
$modify_t = date('Y-m-d H:i:s');

// insert sql
$insertsql = "insert into APP_USER(USERID,LOGINNAME,NAME,EMAIL,CELLPHONE,NOTE,VALID,PASSWORD,CREATETIME,MODIFYTIME) values('{$userid}','{$loginname}','{$name}','{$email}','{$cellphone}','{$note}',{$valid},'{$password}',to_date('{$create_t}','yyyy-mm-dd hh24:mi:ss'),to_date('{$modify_t}','yyyy-mm-dd hh24:mi:ss'))";
//$insertsql = "insert into APP_USER(USERID,LOGINNAME,NAME,EMAIL,CELLPHONE,NOTE,VALID,PASSWORD) values('{$userid}','{$loginname}','{$name}','{$email}','{$cellphone}','{$note}',{$valid},'{$password}')";

//echo $insertsql;
// parse sql
$stid = oci_parse($conn, $insertsql);

// execute sql
$res = oci_execute($stid);

if ($res) {
    echo 'OK';
} else {
    echo 'FAL';
}
