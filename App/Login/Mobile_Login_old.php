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

    private function checkToken($userInfo, $connect) {
        // check token in database
        //$get_token = "select PASSWORD from APP_USER where TOKEN = '{$userInfo['token']}'";
        $get_token = "select USER_ID from APP_USER where TOKEN = '{$userInfo['token']}'";
        //echo $get_token;
        //exit;

        // parse
        $sttk = oci_parse($connect, $get_token);

        // execute
        if (!oci_execute($sttk)) {
			Response::show(406,'Mobile_Login: query database by token error');
        }
        if ($tkrows = oci_fetch_array($sttk, OCI_BOTH)) {
            // login successfully
            //echo "$tkrows is: ";
            //echo $tkrows;
            return $tkrows['USER_ID'];
            //return true;
        } 
        //else {
        //    // token is out of date
		//	Response::show(,'Mobile_Login: token do not exist');
        //    //return false;
        //}
        return false;
    }

    //////////////
    // user provide password to login
    private function checkPassword($userInfo, $connect) {

        // sql example
		//$get_pwd= "select PASSWORD from APP_USER where LOGINNAME ='{$userInfo['loginname']}' OR EMAIL='{$userInfo['email']}' OR CELLPHONE='{$userInfo['cellphone']}'";

        // flag to indentity first condition
        $isFirst = true;
        // generate update sql
        //$get_pwd = "SELECT PASSWORD FROM APP_USER WHERE"; 
        $get_pwd = "SELECT PASSWORD,USER_ID FROM APP_USER WHERE"; 
        //$get_pwd = "SELECT USER_ID FROM APP_USER WHERE"; 
        if (isset($userInfo['loginname'])) {
            if ($userInfo['loginname'] != '') {
                $get_pwd = $get_pwd . " LOGIN_NAME='{$userInfo['loginname']}'";
                $isFirst = false;
            }
        }
        if (isset($userInfo['email'])) {
            if ($userInfo['email'] != '') {
                if ($isFirst) {
                    $get_pwd = $get_pwd . " EMAIL='{$userInfo['email']}'";
                    $isFirst = false;
                } else {
                    $get_pwd = $get_pwd. " OR EMAIL='{$userInfo['email']}'";
                }
            }
        }
        if (isset($userInfo['cellphone'])) {
            if ($userInfo['cellphone'] != '') {
                if ($isFirst) {
                    $get_pwd = $get_pwd . " CELLPHONE='{$userInfo['cellphone']}'";
                    $isFirst = false;
                } else {
                    $get_pwd = $get_pwd . " OR CELLPHONE='{$userInfo['cellphone']}'";
                }
            }
        }

        // parse
        $stpwd = oci_parse($connect, $get_pwd);

        // execute
        if (!oci_execute($stpwd)) {
            // TODO
			Response::show(407,'Mobile_Login-check Password: query database error');
        }

        // get rows 
		if ($pwdrows = oci_fetch_array($stpwd, OCI_BOTH)) {
			if ($pwdrows['PASSWORD'] == $userInfo['password']) {
                // login successfully
				//return true;
                //var_dump($pwdrows);
                // erase space
                $userid = preg_replace("/\s/","",$pwdrows['USER_ID']);
                return $userid;
                //return $pwdrows['USER_ID'];
			}
            // wrong password 
			return false;
		}
		else {
			Response::show(405,"Mobile_Login: user doesn't exist");
		}
    }

    private function generateToken($userInfo, $connect) {
        // generate token
        $token = md5(uniqid(microtime(true),true));

        // sql example
        //$updateToken = "UPDATE APP_USER SET TOKEN='{$token}' WHERE LOGINNAME='{$userInfo['loginname']}' OR EMAIL='{$userInfo['email']}' OR CELLPHONE='{$userInfo['cellphone']}'";

        // flag to indentity first condition
        $isFirst = true;

        // generate update sql
        $updateToken = "UPDATE APP_USER SET TOKEN='{$token}' WHERE";
        if (isset($userInfo['loginname'])) {
            if ($userInfo['loginname'] != '') {
                $updateToken = $updateToken . " LOGIN_NAME='{$userInfo['loginname']}'";
                $isFirst = false;
            }
        }

        if (isset($userInfo['email'])) {
            if ($userInfo['email'] != '') {
                if ($isFirst) {
                    $updateToken = $updateToken . " EMAIL='{$userInfo['email']}'";
                    $isFirst = false;
                } else {
                    $updateToken = $updateToken . " OR EMAIL='{$userInfo['email']}'";
                }
            }
        }

        if (isset($userInfo['cellphone'])) {
            if ($userInfo['cellphone'] != '') {
                if ($isFirst) {
                    $updateToken = $updateToken . " CELLPHONE='{$userInfo['cellphone']}'";
                    $isFirst = false;
                } else {
                    $updateToken = $updateToken . " OR CELLPHONE='{$userInfo['cellphone']}'";
                }
            }
        }

        // parse
        $sttk = oci_parse($connect, $updateToken);

        // execute
        if (!oci_execute($sttk)) {
			Response::show(408,'Mobile_Login-generateToken: query database error, token generate failure');
        }

        oci_free_statement($sttk);

        return $token;
    }

	public function login($userInfo, $connect) {
        // check password
        if (isset($userInfo['password'])) {
            if ($userInfo['password'] != '') {
                if ($userid = $this->checkPassword($userInfo, $connect)) {
                    //echo $userid;
                    // TODO
                    // update token and repsonse to client
                    $token = $this->generateToken($userInfo, $connect);
                    
                    $responseData = array(
                        'token' => $token
                    );

		        	//Response::show(401,'Mobile_Login: login successful by password',$responseData);
                    //return 2;
                    //echo 'pwd';
                    return $userid;
                } else {
		        	Response::show(403,'Mobile_Login: wrong password');
                    //return false;
		        }
            }
        }

        // check token
        if (isset($userInfo['token'])) {
            if ($userInfo['token'] != '') {
                if ($userid = $this->checkToken($userInfo, $connect)) {
                    // response OK message to client
		        	//Response::show(400,'Mobile_Login: login successful by token');
                    //return 1;
                    return $userid;
                } else {
                    // token is out of date
		        	Response::show(402,'Mobile_Login: token is out of date');
                    //return false;
                }
            }
        }

        Response::show(404,'Mobile_Login: lack of password or token');

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


        /*
		// query database for spacific user
		$get_pwd= "select PASSWORD from APP_USER where LOGINNAME ='{$userInfo['loginname']}' OR EMAIL='{$userInfo['email']}' OR CELLPHONE='{$userInfo['cellphone']}'";

        // only token without loginname

        // parse
        $stpwd = oci_parse($connect, $get_pwd);

        // execute
        if (!oci_execute($stpwd) || !oci_execute($sttk)) {
            // TODO
			Response::show(401,'Mobile_Login: query database by name error');
        }

        // get rows 
		if ($pwdrows = oci_fetch_array($stpwd, OCI_BOTH)) {
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
        */
