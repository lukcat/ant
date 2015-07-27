<?php

namespace App\Complaint;

class User_Complaint {
    public function ReceiveComplaint($connect, $params, $user_id) {

        $complaintid = md5(uniqid(microtime(true),true));
        //$userid = $complaint['userid'];             // userid must exist or database would report error
        $userid = $user_id;             // userid must exist or database would report error
        $complaint = $params['complaint'];       // primariy key
        $feedback = 'No feedback yet';
        $valid = 1;
        $type = 0;
        $createtime = date('Y-m-d H:i:s');          // use oracle to_date function to format the date
        $modifytime = date('Y-m-d H:i:s');          // use oracle to_date function to format the date

        $sql="INSERT INTO COMPLAINT(COMPLAINT_ID,USER_ID,COMPLAINT,FEEDBACK,VALID,TYPE,CREATE_TIME,MODIFY_TIME) VALUES ('{$complaintid}','{$userid}','{$complaint}','{$feedback}','{$valid}','{$type}',to_date('{$createtime}','yyyy-mm-dd hh24:mi:ss'),to_date('{$modifytime}','yyyy-mm-dd hh24:mi:ss'))";

        $stid = oci_parse($connect,$sql);

        if(!oci_execute($stid)) {
            return false;
        } 

        return $complaintid;
    }

    protected function getPhotoAddr($hostname,$path,$filename) {
        return $hostname . substr($path,1) . '/' . $filename;
    }

    public function GetComplaint($connect, $userID) {
        // TODO
        // query by userid,and return complaint information
        $sql = "select c.complaint_id, c.complaint, c.feedback, c.create_time, t.path, t.local_name from complaint c,thumbnail t where c.complaint_id=t.complaint_id and user_id='{$userID}'";
        //echo $sql;

        // parse sql
        $stgc = oci_parse($connect, $sql);

        if (!oci_execute($stgc)) {
            return false;
        }

        // init varibles
        $hostName = 'http://192.168.146.88/ant';

        $complaintID = ''; 
        $complaint = ''; 
        $feedback  = ''; 
        $photoPath = ''; 
        $photoName = ''; 
        $createTime= ''; 

        $resData = array('userID' => $userID);
        // Store photos temprarily
        $photoAddrs = array();
        $data = array();
        $complaintInfo = array();

        // get data from database
        if ($gcRows = oci_fetch_array($stgc, OCI_BOTH)) {
            $complaintID = $gcRows['COMPLAINT_ID'];
            $complaint = $gcRows['COMPLAINT'];
            $feedback  = $gcRows['FEEDBACK'];
            $photoPath = $gcRows['PATH'];
            $photoName = $gcRows['LOCAL_NAME'];
            $createTime= $gcRows['CREATE_TIME'];

            $photoAddr = $this->getPhotoAddr($hostName,$photoPath,$photoName);
            array_push($photoAddrs, $photoAddr);
            $complaintInfo = array('complaint' => $complaint,
                        'feedback' => $feedback,
                        'createTime' => $createTime);

            //var_dump($photoAddrs);
            //var_dump($gcRows);
            //echo $this->getPhotoAddr($hostName,$photoPath,$photoName);
        }
        //var_dump($complaintInfo);
            //exit;

        // Push userid to resData
        //$data = array('');
        //var_dump($data);
        //exit;


        while ($gcRows = oci_fetch_array($stgc, OCI_BOTH) ) {
            //$complaint = $gcRows['COMPLAINT_ID'];
            //var_dump($gcRows);
            if ($complaintID == $gcRows['COMPLAINT_ID']) {

                $photoPath = $gcRows['PATH'];
                $photoName = $gcRows['LOCAL_NAME'];
                $photoAddr = $this->getPhotoAddr($hostName,$photoPath,$photoName);
                //echo $photoAddr;

                array_push($photoAddrs, $photoAddr);

            } else {
                $photoURL = array('photoURL' => $photoAddrs);
                $photoAddrs = array();      // delete data
                //$complaintInfo = $complaintInfo + $photoAddrs;
                $complaintInfo = $complaintInfo + $photoURL;
                array_push($data, $complaintInfo);

                $complaintID = $gcRows['COMPLAINT_ID'];
                $complaint = $gcRows['COMPLAINT'];
                $feedback  = $gcRows['FEEDBACK'];
                $photoPath = $gcRows['PATH'];
                $photoName = $gcRows['LOCAL_NAME'];
                $createTime= $gcRows['CREATE_TIME'];

                $photoAddr = $this->getPhotoAddr($hostName,$photoPath,$photoName);
                array_push($photoAddrs, $photoAddr);
                $complaintInfo = array('complaint' => $complaint,
                        'feedback' => $feedback,
                        'createTime' => $createTime);
            }
        }
        $photoURL = array('photoURL' => $photoAddrs);
        $complaintInfo = $complaintInfo + $photoURL;

        array_push($data, $complaintInfo);
        //$data = $data . $photoAddrs;
        $complaintData = array('data' => $data);
        $resData = $resData + $complaintData;
        //var_dump($resData);
        var_dump($resData);

        // return value formate
        /*
        {
            userId: yourid,
            data: [
                {
                    complaint: complaintText1,
                    feedback: feedbackText1,
                    photoURL: ['http://hostname/path/name1.ext','http://hostname/path/name.ext',...],
                    createTime: complaintCreateTime1
                },
                {
                    complaint: complaintText1,
                    feedback: feedbackText1,
                    photoURL: ['http://hostname/path/name3.ext','http://hostname/path/name4.ext',...],
                    createTime: complaintCreateTime1
                },
                ...
            ]
        }
        */
    }
}
