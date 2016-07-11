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

class SM_Register {
    // get userid
    public function getUseridByCellphone($cellphone, $connect) {
        $userid = self::cellphoneExist($cellphone, $connect);
        if ($userid) {
            return $userid;
        } else {
            // insert cellphone as a new user and return userid
            $userid = self::registerByCellphone($cellphone, $connect);
            return $userid;

        }
    }

    // register by cellphone
    private function registerByCellphone($cellphone, $connect) {
        $gu = new Guid();
        $guid = $gu->generateGuid();

        $userid = $guid;
        $valid = 1;
        $type = 1;
        $createtime = date('Y-m-d H:i:s');
        $modifytime = date('Y-m-d H:i:s');

        $insertsql = "INSERT INTO MAPP_USER(USER_ID,CELLPHONE,VALID,TYPE,CREATE_TIME,MODIFY_TIME) VALUES ('{$userid}','{$cellphone}',{$valid},{$type},to_date('{$createtime}','yyyy-mm-dd hh24:mi:ss'),to_date('{$modifytime}','yyyy-mm-dd hh24:mi:ss'))";

        // parse sql
        $stid = oci_parse($connect, $insertsql);

        // execute sql
        if (!oci_execute($stid)) {
            Response::show(2402,'Mobile_Register-Register: insert into database error');
        } 
        
        return $userid;

    }

    public function cellphoneExist($cellphone, $connect) {
        //$cellphone = $userInfo['cellphone'];
		
        // All three property shouldn't be empty
        if ($cellphone !='') {
            // query sentance
            // valid=0 means data is out of date, only valid=1 can be used
		    $check_cp= "SELECT USER_ID FROM MAPP_USER where CELLPHONE='{$cellphone}' AND VALID=1 ";

            // parse sql
            // check cellphone
            $stcp = oci_parse($connect, $check_cp);
            if (!oci_execute($stcp)) {
		        Response::show(508,'Mobile_Register-cellphone2: query database error');
            }
            $cprows = oci_fetch_array($stcp, OCI_BOTH);
            if ($cprows) {
                // loginname already exists
                //return true;
                $userID = isset($cprows['USER_ID']) ? $cprows['USER_ID'] : '';
                ///echo $userID;die();
                if ($userID != '') {
                    return $userID;
                }
                //Response::show(505,"cellphone already exists");
            }

            return false;
        }
    }

}

