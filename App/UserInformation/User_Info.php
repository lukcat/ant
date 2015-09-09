<?php 

namespace App\UserInformation;

// use Common\Response equals to Common\Response as Response
use Common\Response as Response;

class User_Info {
    // get user's email address
    public function getEmail($connect, $userInfo) {
        // query sql
        $loginid = $userInfo['loginid'];
        $emailsql= "SELECT EMAIL FROM APP_USER WHERE LOGIN_NAME='{$loginid}' OR EMAIL='{$loginid}' OR CELLPHONE='{$loginid}'";

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
}
