<?php

namespace App\Complaint;

class User_Complaint {
    public function ReceiveComplaint($connect, $params, $user_id) {

        $complaintid = md5(uniqid(microtime(true),true));
        //$userid = $complaint['userid'];             // userid must exist or database would report error
        $userid = $user_id;             // userid must exist or database would report error
        $complaint = $params['complaint'];       // primariy key
        $valid = 1;
        $type = 0;
        $createtime = date('Y-m-d H:i:s');          // use oracle to_date function to format the date
        $modifytime = date('Y-m-d H:i:s');          // use oracle to_date function to format the date

        $sql="INSERT INTO COMPLAINT(COMPLAINT_ID,USER_ID,COMPLAINT,VALID,TYPE,CREATE_TIME,MODIFY_TIME) VALUES ('{$complaintid}','{$userid}','{$complaint}','{$valid}','{$type}',to_date('{$createtime}','yyyy-mm-dd hh24:mi:ss'),to_date('{$modifytime}','yyyy-mm-dd hh24:mi:ss'))";

        $stid = oci_parse($connect,$sql);

        if(!oci_execute($stid)) {
            return false;
        } 

        return $complaintid;
    }

    public function GetComplaint($connect, $userID) {
        // TODO
    }
}
