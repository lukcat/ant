<?php 

namespace App\UserInformation;

use Common\Response as Response;

class User_ChangePWD {

    // verify user's identity
    public function verifyUser($connect, $userInfo) {
        // get loginid and password
        $loginid = $userInfo['loginid'];
        $password = $userInfo['password'];
        
        if (empty($userInfo['loginid']) || empty($userInfo['password'])) {
            Response::show(1301,'Login id is empty');
        }

        $get_pwd = "SELECT USER_ID FROM MAPP_USER WHERE (LOGIN_NAME='{$loginid}' OR EMAIL='{$loginid}' OR CELLPHONE='{$loginid}') AND PASSWORD='{$password}'"; 

        // parse
        $stpwd = oci_parse($connect, $get_pwd);

        // execute
        if (!oci_execute($stpwd)) {
            // TODO
			Response::show(1302,'User_Modify-check Password: query database error');
        }

        // get rows 
		if ($pwdrows = oci_fetch_array($stpwd, OCI_BOTH)) {
            return true;
        }

	    Response::show(1303,"User_Modify-verifyUser: User doesn't exist OR wrong password");
    }

    // modify user's password
    public function changePassword($connect, $userInfo) {
        if ($this->verifyUser($connect, $userInfo)) {
            // get loginid and password
            $loginid = $userInfo['loginid'];
            //$password = $userInfo['password'];
            $newpassword = $userInfo['newpassword'];

            $updatePwd = "UPDATE MAPP_USER SET PASSWORD='{$newpassword}' WHERE LOGIN_NAME='{$loginid}' OR EMAIL='{$loginid}' OR CELLPHONE='{$loginid}'";

            // parse
            $stup = oci_parse($connect, $updatePwd);

            // execute
            if (!oci_execute($stup)) {
                // TODO
		    	Response::show(1304,'User_Modify-ModifyPassword: query database error');
            }
            // response success message
            Response::show(1300,'User_Modify-ModifyPassword: User password modified successful');
        } 
    }

    //******************************************************************************************//

    public function getUserID($connect, $userInfo) {
        // get loginid and password
        $loginid = $userInfo['loginid'];

        // get userid sql
        $gusql = "SELECT USER_ID FROM MAPP_USER WHERE EMAIL='{$loginid}' OR CELLPHONE='{$loginid}' OR LOGIN_NAME='{$loginid}'";

        // parse
        $stgu = oci_parse($connect, $gusql);

        // execute
        if (!oci_execute($stgu)) {
		    Response::show(1321,'User_Modify-getUserId: query database error');
        }

		if ($gurows = oci_fetch_array($stgu, OCI_BOTH)) {
            $userid = preg_replace("/\s/","",$pwdrows['USER_ID']);
            return $userid;
        }

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

    //***********************************************//
    public function checkSecurityCode($connect, $userid) {
        // check sql
        $checksql = "SELECT SECURITY_CODE, EXPIRATION_TIME FROM MAPP_SECURITY_CODE WHERE USER_ID='{$userid}'";

        // parse
        $stcs = oci_parse($connect, $checksql);

        // execute
        if (!oci_execute($stcs)) {
		    Response::show(1322,'User_Modify-checkUserID: query database error');
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

    // sent security code to user's email address and cellphone
    public function getSecurityCode($connect, $userInfo) {
        // generate security code (4)
        $securityCode = $this->generateRandNumber(4);

        // save it into database
        $userid = $this->getUserID($connect, $userInfo);

        // return to user(email or cellphone)
        
    }

    // verify security code 
    public function verifySecurityCode($connect, $userInfo) {
        // get security code by loginid from databse
        // compare security code 
        // generate a string seed by security code and return it to user
    }

    public function forgetPassword($connect, $userInfo) {
        // Get password by email
        // verify email and login_name
        // sent scurity code to user's email address
        // varidy security code and modify user password
    }
}
