<?php 

namespace App\UserInformation;

use Common\Response as Response;

class User_ResetPWD {

    /* Verify user's password 
     * @param connect oracle database handler
     * @param email user's email address
     * return json data 
     */
    public function verifyEmail($connect, $email) {
		
        // All three property shouldn't be empty
        if ($email !='') {
            // query sentance
            // valid=0 means data is out of date, only valid=1 can be used
		    $check_em= "SELECT USER_ID FROM MAPP_USER where EMAIL='{$email}' OR LOGIN_NAME='{$email}' OR CELLPHONE='{$email}' AND VALID=1";

            // check email
            $stem = oci_parse($connect, $check_em);
            if (!oci_execute($stem)) {
		        Response::show(1231,'Mobile_Register-email: query database error');
            }
            $emrows = oci_fetch_array($stem, OCI_BOTH);
            if ($emrows) {
                return true;
            } else {
                Response::show(1232,"Email do not exists");
            }
        }

        Response::show(1233,"email is empty");
    }

    // generate seed
    //protected function generateSeed($userInfo) {
    protected function generateSeed($email, $securitycode, $timestamp) {
        //$email = $userInfo['email'];
        //$securitycode = $userInfo['securitycode'];
        //$timestamp = $userInfo['timestamp'];

        if (empty($email) || empty($securitycode) || empty($timestamp)) {
            Response::show(1204, 'User_ForgotPWD-generateSeed: Email, sn, security code and timestamp can not be empty');
        }

        $seed = $email.$securitycode.$timestamp;

        return $seed;
    }

    // Varify security code
    public function verifySecurityCode($userInfo) {
        // Get parameters from user
        //$loginid = $userInfo['loginid'];
        $email = $userInfo['email'];
        $securitycode = $userInfo['securitycode'];
        //$newpassword = $userInfo['newpassword'];
        $sn = $userInfo['sn'];
        $timestamp = $userInfo['timestamp'];

        if (empty($email) || empty($sn) || empty($securitycode) || empty($timestamp)) {
            Response::show(1202, 'User_ForgotPWD-verifySecurityCode: Email, sn, security code and timestamp can not be empty');
        }

        // verify serial number(sn)
        // seed for encription
        //$seed = $email.$securitycode.$timestamp;
        //$seed = $this->generateSeed($userInfo);
        $seed = $this->generateSeed($email, $securitycode, $timestamp);

        // Generate serail number
        $newsn = sha1(md5($seed));

        // compare
        if ($sn == $newsn) {
            // successful
            Response::show(1200, 'User_ForgotPWD-verifySecurityCode: Effective security code');
        } else {
            // failure
            Response::show(1201, 'User_ForgotPWD-verifySecurityCode: Invalid security code');
        }
    }


    // Get user id
    public function getUserID($connect, $userInfo) {
        // get loginid and password
        //$loginid = $userInfo['loginid'];
        $email = $userInfo['email'];

        //if (empty($loginid) || empty($email)) {
        if (empty($email)) {
		    Response::show(1126,'User_Forgot-getUserID: email is empty');
        }

        // get userid sql
        $gusql = "SELECT USER_ID FROM MAPP_USER WHERE EMAIL='{$email}'";

        // parse
        $stgu = oci_parse($connect, $gusql);

        // execute
        if (!oci_execute($stgu)) {
		    Response::show(1127,'User_Change-getUserId: query database error');
        }

		if ($gurows = oci_fetch_array($stgu, OCI_BOTH)) {
            $userid = preg_replace("/\s/","",$gurows['USER_ID']);
            return $userid;
        }

		Response::show(1122,'User_Change-getUserId: No such user');

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
        $sql = "SELECT SECURITY_CODE, to_char(EXPIRATION_TIME,'yyyy-mm-dd hh24:mi:ss') AS EXPIRATION_TIME  FROM MAPP_SECURITY_CODE WHERE USER_ID='{$userid}'";

        // parse
        $stgs = oci_parse($connect, $sql);

        // execute
        if (!oci_execute($stgs)) {
			Response::show(1123,'User_ForgotPWD-generateSecurityCode: query database error');
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
                $updatesql = "UPDATE MAPP_SECURITY_CODE SET SECURITY_CODE='{$securityCode}', EXPIRATION_TIME=to_date('{$newexpirationtimestr}','yyyy-mm-dd hh24:mi:ss') WHERE USER_ID='{$userid}'";

                // parse
                $stus = oci_parse($connect, $updatesql);

                // execute
                if (!oci_execute($stus)) {
                    // sql execute failure
			        Response::show(1129,'User_ForgotPWD-generateSecurityCode: query database error');
                }

                // return data
                //$reData = array('expirationtime' => $expirationtime, 'securitycode' => $securityCode);
                $reData = array('expirationtime' => $newexpirationtime, 'securitycode' => $securityCode);

                return $reData;
                //return $securityCode;
                                            
            } else {
                // valid security code
                if (!empty($securitycode)) {
                    $reData = array('expirationtime' => $expirationtime, 'securitycode' => $securitycode);

                    return $reData;
                    //return $securitycode;
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
			            Response::show(1124,'User_ForgotPWD-getSecurityCode: query database error');
                    }

                    // return data
                    $reData = array('expirationtime' => $newexpirationtime, 'securitycode' => $securityCode);

                    return $reData;
                    //return $securityCode;
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
			    Response::show(1125,'User_ForgotPWD-getSecurityCode: query database error');
            }

            // return data
            $reData = array('expirationtime' => $expirationtime, 'securitycode' => $securityCode);

            return $reData;
            //return $securityCode;

        }
    }

    // Generate serial number
    public function generateSerialNumber($email, $securitycode, $timestamp) {
    //public function generateSerialNumber($userInfo) {

        // seed for encription
        //$seed = $email.$securitycode.$timestamp;
        //$seed = $this->generateSeed($userInfo);
        $seed = $this->generateSeed($email, $securitycode, $timestamp);

        // Generate serail number
        $sn = sha1(md5($seed));

        return $sn;
    }

    // Get security code, main function to process get security code 
    public function getSecurityCode($connect, $userInfo) {
        // get userid
        $userid = $this->getUserID($connect, $userInfo);

        // get security code
        //$securitycode = $this->generateSecurityCode($connect, $userid);
        $reData = $this->generateSecurityCode($connect, $userid);
        $securitycode = $reData['securitycode'];
        $expirationtime = $reData['expirationtime'];

        // generate serial number to verify security code
        //$loginid = $userInfo['loginid'];
        $email = $userInfo['email'];

        $sn = $this->generateSerialNumber($email, $securitycode, $expirationtime);
        //$sn = $this->generateSerialNumber($userInfo);

        $resData = array('email' => $email, 'securitycode' => $securitycode, 'sn' => $sn, 'timestamp' => $expirationtime);

        return $resData;
    }

    // Invalid security by update it's expiration time in datebase
    public function invalidSecurityCode($connect, $userInfo) {

        // Get user id 
        $userid = $this->getUserID($connect, $userInfo);

        // Get user's parameter
        $email = $userInfo['email'];

        // Get current time
        $curDate = date('Y-m-d H:i:s');

        // sql
        $setSCDate = "UPDATE SECURITY_CODE SET EXPIRATION_TIME=to_date('{$curDate}','yyyy-mm-dd hh24:mi:ss') WHERE USER_ID='{$userid}'";

        // parse sql
        $ssd = oci_parse($connect, $setSCDate);

        // execute sql
        if (!oci_execute($ssd)) {
            Response::show(1127, 'User_ForgotPWD-ChangePwdBySecurityCode: query database error');
        }
    }

    // 
    public function changePwdBySecurityCode($connect, $userInfo) {

        // Get parameters from user
        //$loginid = $userInfo['loginid'];
        $email = $userInfo['email'];
        $securitycode = $userInfo['securitycode'];
        $newpassword = $userInfo['newpassword'];
        $sn = $userInfo['sn'];
        $timestamp = $userInfo['timestamp'];

        // expiration time equals to timestamp
        $expirationtime = $timestamp;

        if (empty($email) || empty($securitycode) || empty($newpassword) || empty($sn) || empty($timestamp)) {
		    Response::show(1129,'User_ForgotPWD-ChangePwdBySecurityCode: email, securitycode, newpassword, sn, timestamp can not be empty');
        }

        if (empty($email) || empty($securitycode) || empty($newpassword) || empty($sn)) {
            Response::show(1129, "Email, securitycode, newpassword and sn can not be empty");
        }

        // verify serial number(sn)
        // seed for encription
        //$seed = $email.$securitycode.$timestamp;
        //$seed = $this->generateSeed($userInfo);
        $seed = $this->generateSeed($email, $securitycode, $timestamp);

        // Generate serail number
        $newsn = sha1(md5($seed));

        // Get current time and expiration time
        $currenttime = date('Y-m-d H:i:s');

        // Compare time stamp
        $expirationsec = strtotime($expirationtime);
        $currentsec    = strtotime($currenttime);
        if ($expirationsec > $currentsec) {     // valid
            if ($sn == $newsn) {                // compare sn

                // valid sn and security code, then modify password
                //$updatePwd = "UPDATE APP_USER SET PASSWORD='{$newpassword}' WHERE LOGIN_NAME='{$loginid}' OR EMAIL='{$loginid}' OR CELLPHONE='{$loginid}'";
                $updatePwd = "UPDATE APP_USER SET PASSWORD='{$newpassword}' WHERE EMAIL='{$email}'";

                // parse
                $stup = oci_parse($connect, $updatePwd);

                // execute
                if (!oci_execute($stup)) {
                    // TODO
		        	Response::show(1126,'User_ForgotPWD-ChangePwdBySecurityCode: query database error');
                } else {

                    // Invalid security code
                    $this->invalidSecurityCode($connect, $userInfo);

                    // response success message
                    Response::show(1100,'User_ForgotPWD-ChangePwdBySecurityCode: User password modified successful');
                }

            } else {
                // invalid security code 
                Response::show(1128, 'User_ForgotPWD-ChangePwdBySecurityCode:Invlid security code');
            }
        } else {        // security code is out of date
            Response::show(1131, 'User_ForgotPWD-ChangePwdBySecurityCode: security code is out of date');
        }
        
        
    }
    ///////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////
    // verify user's identity
    /*
    public function verifyUser($connect, $userInfo) {
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
			Response::show(1102,'User_Change-check Password: query database error');
        }

        // get rows 
		if ($pwdrows = oci_fetch_array($stpwd, OCI_BOTH)) {
            return true;
        }

	    Response::show(1103,"User_Change-verifyUser: User doesn't exist OR wrong password");
    }
    // modify user's password
    public function modifyPassword($connect, $userInfo) {
        if ($this->verifyUser($connect, $userInfo)) {
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
		    	Response::show(1104,'User_Change-ChangePassword: query database error');
            }
            // response success message
            Response::show(1100,'User_Change-ChangePassword: User password modified successful');
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
		    Response::show(1121,'User_Change-getUserId: query database error');
        }

		if ($gurows = oci_fetch_array($stgu, OCI_BOTH)) {
            $userid = preg_replace("/\s/","",$pwdrows['USER_ID']); return $userid;
        }

		Response::show(1122,'User_Change-getUserId: No such user');

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
        $checksql = "SELECT SECURITY_CODE, EXPIRATION_TIME FROM MAPP_SECURITY_CODE WHERE USER_ID='{$userid}'";

        // parse
        $stcs = oci_parse($connect, $checksql);

        // execute
        if (!oci_execute($stcs)) {
		    Response::show(1122,'User_Change-checkUserID: query database error');
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


    public function forgetPassword($connect, $userInfo) {
        // Get password by email
        // verify email and login_name
        // sent scurity code to user's email address
        // varidy security code and modify user password
    }
    */
}

