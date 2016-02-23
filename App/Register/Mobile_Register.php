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
use Common\Guid as Guid;

class Mobile_Register {
    // check user id in table APP_USER
    public function idNumberExist($idnumber, $connect) {
        //$loginname = $userInfo['loginname'];

        if ($idnumber!='') {
            // query sentance
            // valid=0 means data is out of date, only valid=1 can be used
		    $check_id = "SELECT USER_ID FROM APP_USER where ICARD_ID='{$idnumber}' AND VALID=1";

            // parse sql
            // check loginname
            $stid = oci_parse($connect, $check_id);
            if (!oci_execute($stid)) {
		        Response::show(501,'Mobile_Register-idnumber: query database error');
            }
            $idrows = oci_fetch_array($stid, OCI_BOTH);
            if ($idrows) {
                // idNumber already exists
                Response::show(509,"idNumber already exists");
            }

            return false;
        }
    }
    // check loginname in table APP_USER
    public function loginnameExist($loginname, $connect) {
        //$loginname = $userInfo['loginname'];

        if ($loginname !='') {
            // query sentance
            // valid=0 means data is out of date, only valid=1 can be used
		    $check_ln= "SELECT USER_ID FROM APP_USER where LOGIN_NAME='{$loginname}' AND VALID=1";

            // parse sql
            // check loginname
            $stln = oci_parse($connect, $check_ln);
            if (!oci_execute($stln)) {
		        Response::show(501,'Mobile_Register-loginname: query database error');
            }
            $lnrows = oci_fetch_array($stln, OCI_BOTH);
            if ($lnrows) {
                // loginname already exists
                Response::show(503,"Loginname already exists");
            }

            return false;
        }
    }

    public function emailExist($email, $connect) {
        //$email = $userInfo['email'];
		
        // All three property shouldn't be empty
        if ($email !='') {
            // query sentance
            // valid=0 means data is out of date, only valid=1 can be used
		    $check_em= "SELECT USER_ID FROM APP_USER where EMAIL='{$email}' AND VALID=1";

            // parse sql
            // check email
            $stem = oci_parse($connect, $check_em);
            if (!oci_execute($stem)) {
		        Response::show(507,'Mobile_Register-email: query database error');
            }
            $emrows = oci_fetch_array($stem, OCI_BOTH);
            if ($emrows) {
                // loginname already exists
                Response::show(504,"email already exists");
            }

            return false;
        }
    }

    public function cellphoneExist($cellphone, $connect) {
        //$cellphone = $userInfo['cellphone'];
		
        // All three property shouldn't be empty
        if ($cellphone !='') {
            // query sentance
            // valid=0 means data is out of date, only valid=1 can be used
		    $check_cp= "SELECT USER_ID FROM APP_USER where CELLPHONE='{$cellphone}' AND VALID=1 ";

            // parse sql
            // check cellphone
            $stcp = oci_parse($connect, $check_cp);
            if (!oci_execute($stcp)) {
		        Response::show(508,'Mobile_Register-cellphone: query database error');
            }
            $cprows = oci_fetch_array($stcp, OCI_BOTH);
            if ($cprows) {
                // loginname already exists
                return true;
                //Response::show(505,"cellphone already exists");
            }

            return false;
        }
    }

    public function getUserTypeByCellphone($cellphone, $connect) {
        //$cellphone = $userInfo['cellphone'];
		
        // All three property shouldn't be empty
        $type = '';
        if ($cellphone !='') {
            // query sentance
            // valid=0 means data is out of date, only valid=1 can be used
		    $check_cp= "SELECT TYPE FROM APP_USER where CELLPHONE='{$cellphone}' AND VALID=1 ";

            // parse sql
            // check cellphone
            $stcp = oci_parse($connect, $check_cp);
            if (!oci_execute($stcp)) {
		        Response::show(508,'Mobile_Register-cellphone: query database error');
            }
            $cprows = oci_fetch_array($stcp, OCI_BOTH);
            if ($cprows) {
                // loginname already exists
                //Response::show(505,"cellphone already exists");
                $type = isset($cprows['TYPE']) ? $cprows['TYPE'] : '';
            }

            return $type;
            //return false;
        }
    }

    /* get userID by cellphone number
     * @param cellphone string cellphone number
     * @param connect string connect handler
     * return mixed string(empty or number)
     */
    public function getUserIDByCellphone($cellphone, $connect) {
        //$cellphone = $userInfo['cellphone'];
		
        // All three property shouldn't be empty
        $userID = '';
        if ($cellphone != '') {
            // query sentance
            // valid=0 means data is out of date, only valid=1 can be used
		    $check_cp= "SELECT USER_ID FROM APP_USER where CELLPHONE='{$cellphone}' AND VALID=1 ";

            // parse sql
            // check cellphone
            $stcp = oci_parse($connect, $check_cp);
            if (!oci_execute($stcp)) {
		        Response::show(508,'Mobile_Register-cellphone: query database error');
            }
            $cprows = oci_fetch_array($stcp, OCI_BOTH);
            if ($cprows) {
                // loginname already exists
                //Response::show(505,"cellphone already exists");
                $userID = isset($cprows['USER_ID']) ? $cprows['USER_ID'] : '';
            }

            //return false;
        }
        return $userID;
    }

	public function itemExist($userInfo,$connect) {	
        $loginname = $userInfo['loginname'];
        $email = $userInfo['email'];
        $cellphone = $userInfo['cellphone'];
		
        // All three property shouldn't be empty
        if ($loginname !='' && $email !='' && $cellphone !='') {
            // query sentance
            // valid=0 means data is out of date, only valid=1 can be used
		    $check_ln= "SELECT USER_ID FROM APP_USER where LOGIN_NAME='{$loginname}' OR EMAIL='{$loginname}' OR CELLPHONE='{$loginname}' AND VALID=1";
		    $check_em= "SELECT USER_ID FROM APP_USER where EMAIL='{$email}' OR LOGIN_NAME='{$email}' OR CELLPHONE='{$email}' AND VALID=1";
		    $check_cp= "SELECT USER_ID FROM APP_USER where CELLPHONE='{$cellphone}' OR LOGIN_NAME='{$cellphone}' OR EMAIL='{$cellphone}' AND VALID=1";

            // parse sql
            // check loginname
            $stln = oci_parse($connect, $check_ln);
            if (!oci_execute($stln)) {
		        Response::show(501,'Mobile_Register-loginname: query database error');
            }
            $lnrows = oci_fetch_array($stln, OCI_BOTH);
            if ($lnrows) {
                // loginname already exists
                Response::show(503,"Loginname already exists");
            }

            // check email
            $stem = oci_parse($connect, $check_em);
            if (!oci_execute($stem)) {
		        Response::show(507,'Mobile_Register-email: query database error');
            }
            $emrows = oci_fetch_array($stem, OCI_BOTH);
            if ($emrows) {
                // loginname already exists
                Response::show(504,"email already exists");
            }

            // check cellphone
            $stcp = oci_parse($connect, $check_cp);
            if (!oci_execute($stcp)) {
		        Response::show(508,'Mobile_Register-cellphone: query database error');
            }
            $cprows = oci_fetch_array($stcp, OCI_BOTH);
            if ($cprows) {
                // loginname already exists
                Response::show(505,"cellphone already exists");
            }

            return false;
        }

        Response::show(506,"loginname, email, cellphone is needed at the same time");
	}

    public function insertUserInfo($userInfo, $connect) {
        // system data
        //$userid = md5(uniqid(microtime(true),true));
        // generate GUID
        $gu = new Guid();
        $guid = $gu->generateGuid();

        $userid = $guid;
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

        $insertsql = "insert into APP_USER(USER_ID,LOGIN_NAME,USER_NAME,EMAIL,CELLPHONE,ICARD_ID,NOTE,VALID,PASSWORD,TOKEN,TYPE,CREATE_TIME,MODIFY_TIME) values('{$userid}','{$loginname}','{$name}','{$email}','{$cellphone}','{$icardid}','{$note}',{$valid},'{$password}','{$token}',0,to_date('{$createtime}','yyyy-mm-dd hh24:mi:ss'),to_date('{$modifytime}','yyyy-mm-dd hh24:mi:ss'))";

        // parse sql
        $stid = oci_parse($connect, $insertsql);

        // execute sql
        if (!oci_execute($stid)) {
            Response::show(502,'Mobile_Register-Register: insert into database error');
        } else {
            // response token to client
            $responseData = array(
                    'token' => $token,
                    );

            Response::show(500,'Mobile_Register: register successful', $responseData);
        }
    }
	
    public function updateUserInfo($userInfo, $connect) {
        // system data
        // generate GUID
        $gu = new Guid();
        $guid = $gu->generateGuid();

        $userid = $guid;
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

        //$updatesql = "UPDATE APP_USER SET USER_ID='{$userid}',LOGIN_NAME='{$loginname}',USER_NAME='{$name}',EMAIL='{$email}',CELLPHONE='{$cellphone'},ICARD_ID='{$icardid}',NOTE='{$note}',VALID='{$valid}',PASSWORD='{$password}',TOKEN='{$token}',CREATE_TIME=to_date('{$createtime}','yyyy-mm-dd hh24:mi:ss'),MODIFY_TIME=to_date('{$createtime}','yyyy-mm-dd hh24:mi:ss'))";
        $updatesql = "UPDATE APP_USER SET LOGIN_NAME='{$loginname}',USER_NAME='{$name}',EMAIL='{$email}',ICARD_ID='{$icardid}',NOTE='{$note}',VALID='{$valid}',PASSWORD='{$password}',TOKEN='{$token}',TYPE=0, CREATE_TIME=to_date('{$createtime}','yyyy-mm-dd hh24:mi:ss'),MODIFY_TIME=to_date('{$createtime}','yyyy-mm-dd hh24:mi:ss') where CELLPHONE='{$cellphone}'";

        // parse sql
        $stid = oci_parse($connect, $updatesql);
        // execute sql
        if (!oci_execute($stid)) {
            Response::show(502,'Mobile_Register-Register: update database error');
        } else {
            // response token to client
            $responseData = array(
                    'token' => $token,
                    );

            Response::show(500,'Mobile_Register: register successful', $responseData);
        }
    }

	//function register($username, $password, $connect) 
	function register($userInfo, $connect) {
        $email = $userInfo['email'];
        $loginname = $userInfo['loginname'];
        $cellphone = $userInfo['cellphone'];
        $idnumber = $userInfo['icardid'];

        // get user type
        $type = self::getUserTypeByCellphone($cellphone, $connect);

        if (self::cellphoneExist($cellphone, $connect) && $type=='1') {
            // update user information
            self::updateUserInfo($userInfo, $connect);

        } elseif (!self::emailExist($email, $connect) && !self::loginnameExist($loginname, $connect) && !self::cellphoneExist($cellphone, $connect) && !self::idNumberExist($idnumber, $connect)) {
            // insert user into database
            self::insertUserInfo($userInfo, $connect);

        } else {
            // TODO
            Response::show(505,"cellphone already exists");
            //Response::show(511,'User already exists');
            
        }

        /*
		if (!self::loginnameExist($loginname, $connect) && !self::emailExist($email, $connect) && !self::cellphoneExist($cellphone, $connect)) {
            // Insert user information into database
            self::insertUserInfo($userInfo, $connect);

        } else {    // User exist 
            $type = self::getUserTypeByCellphone($cellphone, $connect);
            if (!empty($type)) {
                // 0 is app user, 1 is sm user
                if ($type == '0') {
                    // app user, already registed
                    Response::show(505,"cellphone already exists");
                } elseif ($type == '1') {
                    // sm user, allowed to register 
                    $userID = self::getUserIDByCellphone($cellphone, $connect);

                    self::updateUserInfo($userInfo, $connect);
                }
            } else {
                Response::show(506,"Invalid user type");
            }
        }
        */
	}
}

