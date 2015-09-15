<?php 

namespace App\UserInformation;

use Common\Response as Response;

class User_ForgetPWD {

    // Get user id
    public function getUserID($connect, $userInfo) {
        // get loginid and password
        $loginid = $userInfo['loginid'];
        $email = $userInfo['email'];

        if (empty($loginid) || empty($email)) {
		    Response::show(1126,'User_Forget-getUserID: loginid Or email is empty');
        }

        // get userid sql
        $gusql = "SELECT USER_ID FROM APP_USER WHERE EMAIL='{$loginid}' OR CELLPHONE='{$loginid}' OR LOGIN_NAME='{$loginid}' AND EMAIL='{$email}'";

        // parse
        $stgu = oci_parse($connect, $gusql);

        // execute
        if (!oci_execute($stgu)) {
		    Response::show(1127,'User_Modify-getUserId: query database error');
        }

		if ($gurows = oci_fetch_array($stgu, OCI_BOTH)) {
            $userid = preg_replace("/\s/","",$gurows['USER_ID']);
            return $userid;
        }

		Response::show(1122,'User_Modify-getUserId: No such user');

    }

    // Generate security code
    public function generateRandNumber($len) {
        $min = 0;
        $max = 9;
        $securityCode = '';

        for ($i=0; $i<$len; $i++) {
            $securityCode .= rand($min, $max);
        }

        return $securityCode;
    }

    // get security code of user, if exist and valid, return security code; else, return false
    public function generateSecurityCode($connect, $userid) {
        // sql 
        $sql = "SELECT SECURITY_CODE, to_char(EXPIRATION_TIME,'yyyy-mm-dd hh24:mi:ss') AS EXPIRATION_TIME  FROM SECURITY_CODE WHERE USER_ID='{$userid}'";

        // parse
        $stgs = oci_parse($connect, $sql);

        // execute
        if (!oci_execute($stgs)) {
			Response::show(1123,'User_ForgetPWD-getSecurityCode: query database error');
        }

        $currenttime = new \DateTime();
        $currenttimestr = $currenttime->format('Y-m-d H:i:s');

        if ($gsRows = oci_fetch_array($stgs, OCI_BOTH)) {
            $securitycode = isset($gsRows['SECURITY_CODE']) ? $gsRows['SECURITY_CODE'] : '';
            $expirationtime = isset($gsRows['EXPIRATION_TIME']) ? $gsRows['EXPIRATION_TIME'] : '';

            //$currenttime = date('Y-m-d H:i:s');
            //$currenttime = new DateTime();
            //$currenttimestr = $currenttime->format('Y-m-d H:i:s');

            $expirationsec = strtotime($expirationtime);
            $currentsec    = strtotime($currenttimestr);

            if ($currentsec > $expirationsec) {
                // Expiration
                // Generate new security code and update database
                $securityCode = $this->generateRandNumber(4);

                $newexpirationtime = $currenttime->modify('+2 day');
                $newexpirationtimestr = $currenttime->format('Y-m-d H:i:s');

                
                // Update sql
                $updatesql = "UPDATE SECURITY_CODE SET SECURITY_CODE='{$securityCode}', EXPIRATION_TIME=to_date('{$newexpirationtimestr}','yyyy-mm-dd hh24:mi:ss') WHERE USER_ID='{$userid}'";

                // parse
                $stus = oci_parse($connect, $updatesql);

                // execute
                if (!oci_execute($stus)) {
                    // sql execute failure
			        Response::show(1124,'User_ForgetPWD-getSecurityCode: query database error');
                }

                return $secutityCode;
                                            
            } else {
                // valid security code
                if (!empty($securitycode)) {
                    return $securitycode;
                } else {         // security code is empty

                    // Generate security code
                    $securityCode = $this->generateRandNumber(4);

                    // Generate expiration time and specify format
                    $newexpirationtime = $currenttime->modify('+2 day');
                    $newexpirationtimestr = $newexpirationtime->format('Y-m-d H:i:s');

                    // Update sql
                    $updatesql = "UPDATE SECURITY_CODE SET SECURITY_CODE='{$securityCode}', EXPIRATION_TIME=to_date('{$newexpirationtimestr}','yyyy-mm-dd hh24:mi:ss') WHERE USER_ID='{$userid}'";

                    // parse
                    $stus = oci_parse($connect, $updatesql);

                    // execute
                    if (!oci_execute($stus)) {
                        // sql execute failure
			            Response::show(1124,'User_ForgetPWD-getSecurityCode: query database error');
                    }

                    return $securityCode;
                }
            }
        } else {         // security code doesn't exist yet

            // generate security code and insert into database

            // Generate security code
            $securityCode = $this->generateRandNumber(4);

            // Generate securitcodeID
            $scid = md5(uniqid(microtime(true),true));


            // Generate expiration time and specify format
            $expirationtime = $currenttime->modify('+2 day');
            $expirationtimestr = $expirationtime->format('Y-m-d H:i:s');

            // security code generate time
            $createtimestr = $currenttime->format('Y-m-d H:i:s');

            // insert sql
            $insertsql = "INSERT INTO SECURITY_CODE(SC_ID, USER_ID, SECURITY_CODE, EXPIRATION_TIME, CREATE_TIME) VALUES('{$scid}', '{$userid}', '{$securityCode}', to_date('{$expirationtimestr}','yyyy-mm-dd hh24:mi:ss'), to_date('{$createtimestr}','yyyy-mm-dd hh24:mi:ss'))";

            // parse
            $stis = oci_parse($connect, $insertsql);

            // execute
            if (!oci_execute($stis)) {
                // sql execute failure
			    Response::show(1125,'User_ForgetPWD-getSecurityCode: query database error');
            }

            return $securityCode;

        }
    }

    // Generate serial number
    public function generateSerialNumber($loginid, $email, $securitycode) {

        // seed for encription
        $seed = $loginid.$email.$securitycode;

        // Generate serail number
        $sn = sha1(md5($seed));

        return $sn;
    }

    // Get security code, main function to process get security code 
    public function getSecurityCode($connect, $userInfo) {
        // get userid
        $userid = $this->getUserID($connect, $userInfo);

        // get security code
        $securitycode = $this->generateSecurityCode($connect, $userid);

        // generate serial number to varify security code
        $loginid = $userInfo['loginid'];
        $email = $userInfo['email'];

        $sn = $this->generateSerialNumber($loginid, $email, $securitycode);

        $resData = array('loginid' => $loginid, 'email' => $email, 'securitycode' => $securitycode, 'sn' => $sn);

        return $resData;
    }

    // varify user's identity
    /*
    public function varifyUser($connect, $userInfo) {
        // get loginid and password
        $loginid = $userInfo['loginid'];
        $password = $userInfo['password'];
        
        if (empty($userInfo['loginid']) || empty($userInfo['password'])) {
            Response::show(1101,'Login id is empty');
        }

        $get_pwd = "SELECT USER_ID FROM APP_USER WHERE LOGIN_NAME='{$loginid}' OR EMAIL='{$loginid}' OR CELLPHONE='{$loginid}' AND PASSWORD='{$password}'"; 

        // parse
        $stpwd = oci_parse($connect, $get_pwd);

        // execute
        if (!oci_execute($stpwd)) {
            // TODO
			Response::show(1102,'User_Modify-check Password: query database error');
        }

        // get rows 
		if ($pwdrows = oci_fetch_array($stpwd, OCI_BOTH)) {
            return true;
        }

	    Response::show(1103,"User_Modify-varifyUser: User doesn't exist OR wrong password");
    }
    // modify user's password
    public function modifyPassword($connect, $userInfo) {
        if ($this->varifyUser($connect, $userInfo)) {
            // get loginid and password
            $loginid = $userInfo['loginid'];
            //$password = $userInfo['password'];
            $newpassword = $userInfo['newpassword'];

            $updatePwd = "UPDATE APP_USER SET PASSWORD='{$newpassword}' WHERE LOGIN_NAME='{$loginid}' OR EMAIL='{$loginid}' OR CELLPHONE='{$loginid}'";

            // parse
            $stup = oci_parse($connect, $updatePwd);

            // execute
            if (!oci_execute($stup)) {
                // TODO
		    	Response::show(1104,'User_Modify-ModifyPassword: query database error');
            }
            // response success message
            Response::show(1100,'User_Modify-ModifyPassword: User password modified successful');
        } 
    }


    public function getUserID($connect, $userInfo) {
        // get loginid and password
        $loginid = $userInfo['loginid'];
        $email = $userInfo['email'];

        // get userid sql
        $gusql = "SELECT USER_ID FROM APP_USER WHERE EMAIL='{$loginid}' OR CELLPHONE='{$loginid}' OR LOGIN_NAME='{$loginid}' AND EMAIL='{$email}'";

        // parse
        $stgu = oci_parse($connect, $gusql);

        // execute
        if (!oci_execute($stgu)) {
		    Response::show(1121,'User_Modify-getUserId: query database error');
        }

		if ($gurows = oci_fetch_array($stgu, OCI_BOTH)) {
            $userid = preg_replace("/\s/","",$pwdrows['USER_ID']); return $userid;
        }

		Response::show(1122,'User_Modify-getUserId: No such user');

    }

    // generate random number serial
    public function generateRandNumber($len) {
        $min = 0;
        $max = 9;
        $securityCode = '';

        for ($i=0; $i<$len; $i++) {
            $securityCode .= rand($min, $max);
        }

        return $securityCode;
    }

    public function checkSecurityCode($connect, $userid) {
        // check sql
        $checksql = "SELECT SECURITY_CODE, EXPIRATION_TIME FROM SECURITY_CODE WHERE USER_ID='{$userid}'";

        // parse
        $stcs = oci_parse($connect, $checksql);

        // execute
        if (!oci_execute($stcs)) {
		    Response::show(1122,'User_Modify-checkUserID: query database error');
        }

        if ($csrow = oci_fetch_array($stcs, OCI_BOTH)) {

        } else {
            // insert security code into database
        }
    }

    public function insertSecuritCode($connect, $userid, $securitycode) {
    }

    public function generateSecurityCode($connect, $userid, $securitycode) {
        // checkuser, whether securityCode is exist and valid
        // if exist and valid, then return SecurityCode
        // if exist and invalid, then update and return securityCode
        // if don't exist, then insert userid and securityCode
    }

    // Generate security code
    public function getSecurityCode($connect, $userInfo) {
        // generate security code (4)
        $securityCode = $this->generateRandNumber(4);

        // save it into database
        $userid = $this->getUserID($connect, $userInfo);

        // return to user(email or cellphone)
        
    }

    // varify security code 
    public function varifySecurityCode($connect, $userInfo) {
        // get security code by loginid from databse
        // compare security code 
        // generate a string seed by security code and return it to user
    }

    public function forgetPassword($connect, $userInfo) {
        // Get password by email
        // varify email and login_name
        // sent scurity code to user's email address
        // varidy security code and modify user password
    }
    */
}

