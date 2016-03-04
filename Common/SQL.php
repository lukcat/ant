<?php
/*
 * CommonAPI.php
 * Description: sql to operate oracle database
 *  Created on: 2015/7/10
 *      Author: Chen Deqing
 */

namespace Common;

class SQL {

    /**
    * Insert user's complaint into database
    * @param Oracle $connect: database handler
    * @param Array $complaint: user's compaint information
    */
    public function insertComplaint($connect, $complaint) {

        $complaintid = md5(uniqid(microtime(true),true));
        $userid = $compliant['userid'];
        $complaint = $complaint['complaint'];
        $valid = 1;
        $type = 0;
        $createtime = date('Y-m-d H:i:s');
        $modifytime = date('Y-m-d H:i:s');

        $sql="INSERT INTO COMPLAINT(COMPLAINTID,USER_USERID,COMPLAINT,VALID,TYPE,CREATETIME,MODIFYTIME) VALUES ('{$complaintid}','{$userid}','{$complaint}','{$valid}','{$type}','{$createtime}','{$modifytime}')";

        $stid = oci_parse($connect,$sql);

        if(!oci_execute($stid)) {
            return false;
        } else {
            return $complaintid;
        }
    }

    /**
    * update user's complaint, add feedback from ant
    * @param Oracle $connect: database handler
    * @param Array $feedback: feedback from ant
    */
    public function updateFeedback($connect, $feedback) {

    }

    /**
    * Insert image's basic information to database
    * @param Oracle $connect: database handler
    * @param Array $imageInfo: image basic information
    */
    public function insertImageInfo($connect,$imageInfo) {

        $photoid = md5(uniqid(microtime(true),true));   // primary key
        $complaintid = $imageInfo['complaintid'];       // foreign key
        $localname = $imageInfo['localname'];           // file name in local system
        $originname = $imageInfo['originname'];         // file origin name
        $size = $imageInfo['size'];                     // file size
        $valid = 1;                                     // 1 represent effective, 0 reprensent ineffective
        $path = $imageInfo['path'];                     // file's relative path
        $description = $imageInfo['description'];       // file description
        $createtime = date('Y-m-d H:i:s');              // file's create_time
        $modifytime = date('Y-m-d H:i:s');              // file's modify_time

        $sql="INSERT INTO PHOTO(PHOTOID,COMMPLAINTID,LOCALNAME,ORIGINNAME,SIZE,TYPE,VALID,PATH,DESCRIPTION,CREATETIME,MODIFYTIME) VALUES ('{$photoid}','{$compaintid}','{$localname}','{$originname}','{$size}','{$valid}','{$path}','{$description}','{$createtime}','{$modifytime}')";

        $stid = oci_parse($connect,$sql);

        if(!oci_execute($stid)) {
            return false;
        } else {
            return $photoid;
        }
    }

    /**
    * Insert thumbnail image's basic information to database
    * @param Oracle $connect: database handler
    * @param Array $imageInfo: thumbnail image's basic information
    */
    public function insertThumbnailInfo($connect, $imageInfo) {
    }

    /**
    * Insert user's information to database
    * @param Oracle $connect: database handler
    * @param Array $userInfo: user's basic information
    */
    public function insertUserInfo($connect, $userInfo) {
    }

    /**
    * Varify user's information from database, for user login
    * @param Oracle $connect: database handler
    * @param Array $params: user's loginname/cellphone/email
    */
    public function varifyUser($connect, $params) {
    }

}

// for test

// test data
$compliant['userid'] = "chendq"
$complaint['complaint'] = "This is man is so nice!";

$ts = new SQL();
$ts->insertComplaint($connect,$complaint);

//public function insertComplaint($connect, $complaint) {



