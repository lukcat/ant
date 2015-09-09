<?php
/*
 * Mobile_Registr.php
 * Description: This is program main entrance, according to user action and calling relevant module
 *  Created on: 2015/4/10
 *      Author: Chen Deqing
 */

namespace App\Register;

// USE ALISE: use Common\Response EQUALS TO use Common\Response as Response
use Common\Response as Response;

class Mobile_Register {
    // check loginname in table APP_USER
	//private function itemExist($loginname, $email, $cellphone, $connect) 	
	function itemExist($userInfo,$connect) {	
        $loginname = $userInfo['loginname'];
        $email = $userInfo['email'];
        $cellphone = $userInfo['cellphone'];
		
        // All three property shouldn't be empty
        if ($loginname !='' && $email !='' && $cellphone !='') {
            // query sentance
            // valid=0 means data is out of date, only valid=1 can be used
		    $check_ln= "SELECT * FROM APP_USER where LOGIN_NAME='{$loginname}' AND VALID=1";
		    $check_em= "SELECT * FROM APP_USER where EMAIL='{$email}' AND VALID=1";
		    $check_cp= "SELECT * FROM APP_USER where CELLPHONE='{$cellphone}' AND VALID=1";

            // parse sql

            // check loginname
            $stln = oci_parse($connect, $check_ln);
            if (!oci_execute($stln)) {
		        Response::show(501,'Mobile_Register: query database error');
            }
            $lnrows = oci_fetch_array($stln, OCI_BOTH);
            //if (!empty($lnrows)) {
            if ($lnrows) {
                // loginname already exists
                Response::show(503,"Loginname already exists");
            }

            // check email
            $stem = oci_parse($connect, $check_em);
            if (!oci_execute($stem)) {
		        Response::show(501,'Mobile_Register: query database error');
            }
            $emrows = oci_fetch_array($stem, OCI_BOTH);
            //if (!empty($emrows)) {
            if ($emrows) {
                // loginname already exists
                Response::show(504,"email already exists");
            }

            // check cellphone
            $stcp = oci_parse($connect, $check_cp);
            if (!oci_execute($stcp)) {
		        Response::show(501,'Mobile_Register: query database error');
            }
            $cprows = oci_fetch_array($stcp, OCI_BOTH);
            //if (!empty($cprows)) {
            if ($cprows) {
                // loginname already exists
                Response::show(505,"cellphone already exists");
            }

            return false;
        }

        Response::show(506,"loginname, email, cellphone is needed at the same time");
	}
	
	//function register($username, $password, $connect) 
	function register($userInfo, $connect) {
		if (!self::itemExist($userInfo, $connect)) {

            // system data
            $userid = md5(uniqid(microtime(true),true));
            $valid = 1; // 1 represent effective
            $createtime = date('Y-m-d H:i:s');
            $modifytime = date('Y-m-d H:i:s');

            // format user information
            $loginname = $userInfo['loginname'];
            $name = $userInfo['name'];
            $email = $userInfo['email'];
            $cellphone = $userInfo['cellphone'];
            $note = $userInfo['note'];
            $password = $userInfo['password'];
            $icardid = $userInfo['icardid'];

            if (empty($password)) {
                Response::show(506,"Password is invalid");
                //return false;
            }

            // generate token
            $token = md5(uniqid(microtime(true),true));

            $insertsql = "insert into APP_USER(USER_ID,LOGIN_NAME,USER_NAME,EMAIL,CELLPHONE,ICARD_ID,NOTE,VALID,PASSWORD,TOKEN,CREATE_TIME,MODIFY_TIME) values('{$userid}','{$loginname}','{$name}','{$email}','{$cellphone}','{$icardid}','{$note}',{$valid},'{$password}','{$token}',to_date('{$createtime}','yyyy-mm-dd hh24:mi:ss'),to_date('{$modifytime}','yyyy-mm-dd hh24:mi:ss'))";

            // parse sql
            $stid = oci_parse($connect, $insertsql);
            
            // execute sql
            if (!oci_execute($stid)) {
			    Response::show(502,'Mobile_Register: inset into database error');
            } else {
                // response token to client
                $responseData = array(
                    'token' => $token,
                );

				Response::show(500,'Mobile_Register: register successful',$responseData);
            }
        }
	}
}

/* test
  * 测试用户注册
*/
/* 去掉namespace
require_once('/var/www/html/ant/Common/Oracle.php');
require_once('/var/www/html/ant/Common/Response.php');
//use Common\Response as Response;
//require_once(BASEDIR . '/../../Common/Db.php');
// 生成数据库句柄
//$connect = Common\Db::getInstance()->connect();
try {
	$connect = Common\Oracle::getInstance()->connect();
} catch (Exception $e) {
	echo "error ocurrs: " , $e;
    Response::show(0,'test');
}
//$userid = md5(uniqid(microtime(true),true));
$userInfo['loginname'] = 'chendq';
$userInfo['email'] = 'chendq@test.com';
$userInfo['cellphone'] = '12345678901';
$userInfo['name'] = 'chendeqing';
$userInfo['note'] = 'lanren';
$userInfo['password'] = sha1(md5('test'));


//$rg = new App/Register/Mobile_Register();
$rg = new Mobile_Register();
//$rg->register('chendq', '123', $connect);
$rg->register($userInfo,$connect);
*/




