<?php

namespace App\Complaint;

use Common\Response as Response;

class User_Complaint {
    public function ReceiveComplaint($connect, $params, $user_id, $complaintid) {

        //$complaintid = md5(uniqid(microtime(true),true));
        //$userid = $complaint['userid'];             // userid must exist or database would report error
        $userid = $user_id;             // userid must exist or database would report error
        $complaint = $params['complaint'];       // primariy key
        $complainttype = $params['complainttype'];
        $vehicleid = $params['vehicleid'];
        $feedback = 'No feedback yet';
        $valid = 1;
        $type = 0;
        $createtime = date('Y-m-d H:i:s');          // use oracle to_date function to format the date
        $modifytime = date('Y-m-d H:i:s');          // use oracle to_date function to format the date

        // sql sentence
        $sql="INSERT INTO COMPLAINT(COMPLAINT_ID,USER_ID,COMPLAINT,TYPE,VEHICLE_ID,FEEDBACK,VALID,CREATE_TIME,MODIFY_TIME) VALUES ('{$complaintid}','{$userid}','{$complaint}','{$complainttype}','{$vehicleid}','{$feedback}','{$valid}',to_date('{$createtime}','yyyy-mm-dd hh24:mi:ss'),to_date('{$modifytime}','yyyy-mm-dd hh24:mi:ss'))";

        $stid = oci_parse($connect,$sql);

        if(!oci_execute($stid)) {
            Response::show(603,'User_Complaint: Query database error');
            //return false;
        } 

        $res = array(
                'ComplaintId' => $complaintid,
                'UserId' => $userid,
                'ComplaintType' => $complainttype,
                'CreateTime' => $createtime,
                'Complaint' => $complaint,
                'vehicleid' => $vehicleid
                );

        return $res;
    }

    // generate complaint id
    public function generateComplaintID() {
        
        mt_srand((double) microtime() * 1000000);
        $minNum = 1;
        $maxNum = 99999999;
        $complaintid = date('Ymdhis') . str_pad(mt_rand($minNum, $maxNum), 5, '0', STR_PAD_LEFT);
        //echo $complaintid;die();

        // old version
        //$complaintid = md5(uniqid(microtime(true),true));
        return $complaintid;
    }

    protected function getPhotoAddr($hostname,$path,$filename) {
        return $hostname . substr($path,1) . '/' . $filename;
    }

    public function GetComplaint($connect, $userID, $hostName) {
        // TODO
        // query by userid,and return complaint information
        //$sql = "select c.complaint_id, c.complaint, c.feedback, c.create_time, t.path, t.local_name from complaint c,thumbnail t where c.complaint_id=t.complaint_id and user_id='{$userID}'";
        // valid equal to 1 means the data is effective
        //$sql_bak = "select c.complaint_id, c.complaint, c.feedback, c.create_time, t.path, t.local_name from complaint c,thumbnail t where c.complaint_id=t.complaint_id and c.user_id='{$userID}' and c.valid=1";
        //$sql = "select c.complaint_id, c.complaint, c.feedback, to_char(c.create_time,'yyyy-mm-dd hh24:mi:ss') as create_time, t.path, t.local_name from complaint c,thumbnail t where c.complaint_id=t.complaint_id and c.user_id='{$userID}' and c.valid=1";
        //$sql = "select c.complaint_id, c.complaint, c.feedback, to_char(c.create_time,'yyyy-mm-dd hh24:mi:ss') as create_time, t.path, t.local_name from complaint c left join thumbnail t on c.complaint_id=t.complaint_id where c.user_id='{$userID}' and c.valid=1";
        $sql = "select c.complaint_id, c.complaint, c.type, c.vehicle_id, c.feedback, to_char(c.create_time,'yyyy-mm-dd hh24:mi:ss') as create_time, t.photo_id, t.path, t.local_name from complaint c left join thumbnail t on c.complaint_id=t.complaint_id where c.user_id='{$userID}' and c.valid=1";
        //echo $sql;
        //exit;

        // parse sql
        $stgc = oci_parse($connect, $sql);

        if (!oci_execute($stgc)) {
            Response::show(704,'User_Complaint-getComplaint: Query database error');
        }

        // init varibles
        //$hostName = 'http://192.168.146.88/ant';
        //$hostName = 'http://172.16.0.49/ant';
        $hostName = 'http://' . $hostName;

        $complaintID = ''; 
        $complaint = ''; 
        $vehicleID = '';
        $feedback  = ''; 
        $photoPath = ''; 
        $photoName = ''; 
        $createTime= ''; 

        // flag
        $hasComplaint = false;

        $resData = array('userID' => $userID);
        // Store photos temprarily
        $photoAddrs = array();
        // Store photos' origin ID
        $photoOriginIDs = array();

        $data = array();
        $complaintInfo = array();

        // get data from database
        if ($gcRows = oci_fetch_array($stgc, OCI_BOTH)) {
            //var_dump($gcRows);
            // user has complaint
            $hasComplaint = true;

            // erase space in head and tail
            $complaintID = preg_replace("/\s/","",$gcRows['COMPLAINT_ID']);
            $photoID = preg_replace("/\s/","",$gcRows['PHOTO_ID']);
            // Store user complaintID
            //$resData = array('userID' => $userID);

            $complaint = isset($gcRows['COMPLAINT']) ? $gcRows['COMPLAINT'] : '';
            $complainttype = isset($gcRows['TYPE']) ? $gcRows['TYPE'] : '';
            $vehicleID = isset($gcRows['VEHICLE_ID']) ? $gcRows['VEHICLE_ID'] : '';
            $feedback  = isset($gcRows['FEEDBACK']) ? $gcRows['FEEDBACK'] : '';
            $photoPath = isset($gcRows['PATH']) ? $gcRows['PATH'] : '';
            $photoName = isset($gcRows['LOCAL_NAME']) ? $gcRows['LOCAL_NAME'] : '';
            $createTime= isset($gcRows['CREATE_TIME']) ? $gcRows['CREATE_TIME'] : '';

            if (!empty($photoPath)) {
                $photoAddr = $this->getPhotoAddr($hostName,$photoPath,$photoName);
            } else {
                $photoAddr = '';
            }

            array_push($photoAddrs, $photoAddr);
            array_push($photoOriginIDs, $photoID);

            $complaintInfo = array(
                        'complaintID' => $complaintID,
                        'complaint' => $complaint,
                        'complaintType' => $complainttype,
                        'vehicleID' => $vehicleID,
                        'feedback' => $feedback,
                        'createTime' => $createTime);
        }

        while ($gcRows = oci_fetch_array($stgc, OCI_BOTH) ) {
            //$complaint = $gcRows['COMPLAINT_ID'];
            //var_dump($gcRows);
            $nextComplaintID = preg_replace("/\s/","",$gcRows['COMPLAINT_ID']);

            if ($complaintID == $nextComplaintID) {

                // photo path array
                $photoPath = $gcRows['PATH'];
                $photoName = $gcRows['LOCAL_NAME'];

                if (!empty($photoPath)) {
                    $photoAddr = $this->getPhotoAddr($hostName,$photoPath,$photoName);
                } else {
                    $photoAddr = '';
                }
                //$photoAddr = $this->getPhotoAddr($hostName,$photoPath,$photoName);
                array_push($photoAddrs, $photoAddr);

                // origin photo id array
                $photoID = preg_replace("/\s/","",$gcRows['PHOTO_ID']);
                array_push($photoOriginIDs, $photoID);

            } else {
                $photoURL = array('photoURL' => $photoAddrs);
                $photoOriginID = array('photoOriginID' => $photoOriginIDs);

                $photoAddrs = array();      // delete data
                $photoOriginIDs = array();  // delete data

                $complaintInfo = $complaintInfo + $photoURL;
                $complaintInfo = $complaintInfo + $photoOriginID;

                array_push($data, $complaintInfo);

                //$complaintID = $gcRows['COMPLAINT_ID'];
                // erase space in head and tail
                $complaintID = preg_replace("/\s/","",$gcRows['COMPLAINT_ID']);
                $photoID = preg_replace("/\s/","",$gcRows['PHOTO_ID']);

                //$complaint = $gcRows['COMPLAINT'];
                //$feedback  = $gcRows['FEEDBACK'];
                //$photoPath = $gcRows['PATH'];
                //$photoName = $gcRows['LOCAL_NAME'];
                //$createTime= $gcRows['CREATE_TIME'];
                $complaint = isset($gcRows['COMPLAINT']) ? $gcRows['COMPLAINT'] : '';
                $complainttype = isset($gcRows['TYPE']) ? $gcRows['TYPE'] : '';
                $vehicleID = isset($gcRows['VEHICLE_ID']) ? $gcRows['VEHICLE_ID'] : '';
                $feedback  = isset($gcRows['FEEDBACK']) ? $gcRows['FEEDBACK'] : '';
                $photoPath = isset($gcRows['PATH']) ? $gcRows['PATH'] : '';
                $photoName = isset($gcRows['LOCAL_NAME']) ? $gcRows['LOCAL_NAME'] : '';
                $createTime= isset($gcRows['CREATE_TIME']) ? $gcRows['CREATE_TIME'] : '';

                if (!empty($photoPath)) {
                    $photoAddr = $this->getPhotoAddr($hostName,$photoPath,$photoName);
                } else {
                    $photoAddr = '';
                }
                //$photoAddr = $this->getPhotoAddr($hostName,$photoPath,$photoName);
                array_push($photoAddrs, $photoAddr);

                // origin photo id array
                $photoID = preg_replace("/\s/","",$gcRows['PHOTO_ID']);
                array_push($photoOriginIDs, $photoID);

                $complaintInfo = array(
                        'complaintID' => $complaintID,
                        'complaint' => $complaint,
                        'complaintType' => $complainttype,
                        'vehicleID' => $vehicleID,
                        'feedback' => $feedback,
                        'createTime' => $createTime);
            }
        }

        $photoURL = array('photoURL' => $photoAddrs);
        $photoOriginID = array('photoOriginID' => $photoOriginIDs);

        $complaintInfo = $complaintInfo + $photoURL;
        $complaintInfo = $complaintInfo + $photoOriginID;

        array_push($data, $complaintInfo);
        //$data = $data . $photoAddrs;
        //var_dump($resData);
        if ($hasComplaint) {
            $complaintData = array('data' => $data);
            $resData = $resData + $complaintData;

            return $resData;
            //var_dump($resData);
        } else {
            $data = '';
            $complaintData = array('data' => $data);
            $resData = $resData + $complaintData;

            return $resData;
            //var_dump($resData);
        }

        // return value formate
        /*
        {
            userId: yourid,
            complaintId: yourcomplaintid,
            data: [
                {
                    complaintid: 'id1',
                    complaint: complaintText1,
                    feedback: feedbackText1,
                    photoURL: ['http://hostname/path/name1.ext','http://hostname/path/name.ext',...],
                    createTime: complaintCreateTime1
                },
                {
                    complaintid: 'id2',
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

    // Delete specific user's complaint 
    public function deleteComplaint($connect, $userInfo) {
        //get complaintID from userInfo
        $complaintID = $userInfo['complaintid'];
        if (!empty($complaintID)) {
            // update database and set valid column equals to 0
            $update_sql = "UPDATE COMPLAINT SET VALID=0 WHERE COMPLAINT_ID='{$complaintID}'";

            // parse
            $dcid = oci_parse($connect, $update_sql);

            // execute
            if(!oci_execute($dcid)) {
            Response::show(805,'User_Complaint-deleteComplaint: Query database error');
                //echo 'db';
                //return false;
            } 
            return true;
        }
        else {
            // complaintid is illegal
            //echo 'string';
            return false;
        }
    }
}

/*
$userInfo['test'] = 'test';
$userInfo['complaintid'] = '0';
$uc =new User_Complaint(); 
$uc->deleteComplaint($userInfo);
*/
