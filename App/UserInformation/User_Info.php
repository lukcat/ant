<?php 

namespace App\UserInformation;

// use Common\Response equals to Common\Response as Response
use Common\Response as Response;

class User_Info {
    // get user's email address
    public function getEmail($connect, $userInfo) {
        // query sql
        $loginid = $userInfo['loginid'];
        $token = $userInfo['token'];
        $emailsql= "SELECT EMAIL FROM APP_USER WHERE LOGIN_NAME='{$loginid}' OR EMAIL='{$loginid}' OR CELLPHONE='{$loginid}' OR TOKEN='{$token}'";

        // parse
        $stes = oci_parse($connect, $emailsql);
        //execute
        if (!oci_execute($stes)) {
            Response::show(101, "UserInfo-GetEmailAddress: query database error");
        }

        // get rows
        if ($esrows = oci_fetch_array($stes, OCI_BOTH)) {
            // email exist
            return $esrows['EMAIL'];
        }

        // information not exist
        return false;
    }

    // For user center, get user's basic information
    public function getUserBasicInfo($connect, $userInfo) {
        // global variable
        $resData = array();

        // get user's login information
        //// login by loginid&password OR token
        $loginid = $userInfo['loginid'];
        $password = $userInfo['password'];
        $token = $userInfo['token'];

        // Get basic information
        if (!empty($loginid) && !empty($password)) {
        $basicsql = "SELECT LOGIN_NAME, USER_NAME, EMAIL, CELLPHONE, ICARD_ID, to_char(CREATE_TIME,'yyyy-mm-dd hh24:mi:ss') AS CREATE_TIME, to_char(MODIFY_TIME,'yyyy-mm-dd hh24:mi:ss') AS MODIFY_TIME FROM APP_USER WHERE LOGIN_NAME='{$loginid}' OR EMAIL='{$loginid}' OR CELLPHONE='{$loginid}' AND PASSWORD='{$password}'";

        } elseif (!empty($token)) {

        $basicsql = "SELECT LOGIN_NAME, USER_NAME, EMAIL, CELLPHONE, ICARD_ID, to_char(CREATE_TIME,'yyyy-mm-dd hh24:mi:ss') AS CREATE_TIME, to_char(MODIFY_TIME,'yyyy-mm-dd hh24:mi:ss') AS MODIFY_TIME FROM APP_USER WHERE TOKEN='{$token}'";
        
        } else {

            // user should post loginid&password OR token
            Response::show(104,"UserInformation-getBasicInfo: No loginid&password OR token is specified");
        }
        //$basicsql = "SELECT LOGIN_NAME, USER_NAME, EMAIL, ICARD_ID, to_char(CREATE_TIME,'yyyy-mm-dd hh24:mi:ss') AS CREATE_TIME, to_char(MODIFY_TIME,'yyyy-mm-dd hh24:mi:ss') AS MODIFY_TIME FROM APP_USER WHERE LOGIN_NAME='{$loginid}' OR EMAIL='{$loginid}' OR CELLPHONE='{$loginid}'";

        // parse
        $stbs = oci_parse($connect, $basicsql);

        // Execute
        if (!oci_execute($stbs)) {
            Response::show(102, "UserInfo-getBasicInfo: query database error");
        }

        // Get data
        if ($bsRow = oci_fetch_array($stbs, OCI_BOTH)) {
            $resData['loginname'] = isset($bsRow['LOGIN_NAME']) ? $bsRow['LOGIN_NAME'] : '';
            $resData['username'] = isset($bsRow['USER_NAME']) ? $bsRow['USER_NAME'] : '';
            $resData['email'] = isset($bsRow['EMAIL']) ? $bsRow['EMAIL'] : '';
            $resData['cellphone'] = isset($bsRow['CELLPHONE']) ? $bsRow['CELLPHONE'] : '';
            $resData['idnumber'] = isset($bsRow['ICARD_ID']) ? $bsRow['ICARD_ID'] : '';
            $resData['createtime'] = isset($bsRow['CREATE_TIME']) ? $bsRow['CREATE_TIME'] : '';
            $resData['modifytime'] = isset($bsRow['MODIFY_TIME']) ? $bsRow['MODIFY_TIME'] : '';

            // Response data to client
            Response::show(100, "UserInfo-getBasicInfo: Get user's information successful", $resData);
        } else {
            Response::show(103, "UerInformation-getBasicInfo: Wrong loginid&password OR token");
        }
    }
}
