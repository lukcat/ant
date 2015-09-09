<?php 

namespace App\UserInformation;

use Common\Response as Response;

class User_Modify {

    public function modifyPassword($connect, $userInfo) {
        // get loginid and password
        $loginid = $userInfo['loginid'];
        $password = $userInfo['password'];

        // update password
        $newpassword = $userInfo['newpassword'];

        if (empty($userInfo['loginid'])) {
            Response::show(1110,'Login id is empty');
        }

        if (empty($userInfo['password'])) {
            Response::show(1111,'Password is empty');
        }

        if (empty($userInfo['newpassword'])) {
            Response::show(1112,'New password is empty');
        }

        $get_pwd = "SELECT PASSWORD,USER_ID FROM APP_USER WHERE LOGIN_NAME='{$loginid}' OR EMAIL='{$loginid}' OR CELLPHONE='{$loginid}'"; 

        // parse
        $stpwd = oci_parse($connect, $get_pwd);

        // execute
        if (!oci_execute($stpwd)) {
            // TODO
			Response::show(1107,'User_Modify-check Password: query database error');
        }

        // get rows 
		if ($pwdrows = oci_fetch_array($stpwd, OCI_BOTH)) {
			if ($pwdrows['PASSWORD'] == $userInfo['password']) {

                // Generate update sql
                $updatePwd = "UPDATE APP_USER SET PASSWORD='{$newpassword}' WHERE LOGIN_NAME='{$loginid}' OR EMAIL='{$loginid}' OR CELLPHONE='{$loginid}'";

                // parse
                $stup = oci_parse($connect, $updatePwd);

                // execute
                if (!oci_execute($stup)) {
                    // TODO
		        	Response::show(1107,'User_Modify-ModifyPassword: query database error');
                }
                // response success message
                Response::show(1100,'User_Modify-ModifyPassword: User password modified successful');
			}
            // wrong password 
            Response::show(1108,'User_Modify-ModifyPassword: Wrong password');
		}
		else {
			Response::show(1109,"Mobile_Login: User doesn't exist");
		}
    }
}
