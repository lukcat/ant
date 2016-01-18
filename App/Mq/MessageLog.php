<?php

namespace App\Mq;

class MessageLog {

    /* Write Mq message into database
     * @param $connect database connection handler
     * @param $guid a globally unique identifier
     * @param $complaintid user's complaint id
     */
    public function writeMessageLog($connect, $guid, $complaintid) {
        
        $id = $guid;
        $mess_id = $complaintid;
        $receive_date = date('Y-m-d H:i:s');
        $mess_flag = 4; // mobile
        
        // sql sentence
        $sql = "INSERT INTO MESS_LOG(ID, MESS_ID, RECEIVE_DATE, MESS_FLAG) VALUES ('{$id}', '{$mess_id}', to_date('{$receive_date}','yyyy-mm-dd hh24:mi:ss'), $mess_flag)";

        // parse
        $stid = oci_parse($connect, $sql);

        // execute
        if (!oci_execute($stid)) {
            return false;
        }

        return true;
    }
}
