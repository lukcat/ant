<?php
/*
 * Mobile_Login.php
 * Description: This module is mainly for mobile user login
 *  Created on: 2015/4/10
 *      Author: Chen Deqing
 */

namespace App\Login;

// use Common\Response equals to Common\Response as Response
use Common\Response as Response;

class Mobile_Login {

	public function login($userInfo, $connect) {
		// query database for spacific user
		$find_sql = "select PASSWORD from APP_USER where LOGINNAME ='{$userInfo['loginname']}' OR EMAIL='{$userInfo['email']}' OR CELLPHONE='{$userInfo['cellphone']}'";

        // parse
        $stid = oci_parse($connect, $find_sql);

        // execute
        if (!oci_execute($stid)) {
            // TODO
			Response::show(401,'Mobile_Login: query database by name error');
        }

        // get rows 
		if ($rows = oci_fetch_array($stid, OCI_BOTH)) {
			//if ($rows['password'] == $check->params['password']) {
			if ($rows['PASSWORD'] == $userInfo['password']) {
				// response message to client
				// TODO
				// 产生token，返回给用户，这部分后期完善
				Response::show(400,'Mobile_Login: login successful');

				return true;
			}
			else {
				Response::show(402,'Mobile_Login: password error');
				//return false;
			}
		}
		else {
			// response message to client, include token
			// TODO
			Response::show(403,'Mobile_Login: user do not exist');
		}
	}
}

/* test 
  * 测试Mobile_Login
*/

/*
require_once('/var/www/html/ant/Common/Oracle.php');
require_once('/var/www/html/ant/Common/Response.php');

// 生成数据库句柄
//$connect = Common\Oracle::getInstance()->connect();
try {
	$connect = Common\Oracle::getInstance()->connect();
} catch (Exception $e) {
	echo "error ocurrs: " , $e;
}

$ml = new Mobile_Login();
$ln = 'chendq';
$pwd = sha1(md5("test"));
if ($ml->varify_loginname($ln, $pwd, $connect)) {
    echo "OK";
} else {
    echo "FAL";
}
*/

