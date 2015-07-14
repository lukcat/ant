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
    * Test result: This module is OK
    */
    public function insertComplaint($connect, $complaint) {

        $complaintid = md5(uniqid(microtime(true),true));
        $userid = $complaint['userid'];             // userid must exist or database would report error
        $complaint = $complaint['complaint'];       // primariy key
        $valid = 1;
        $type = 0;
        $createtime = date('Y-m-d H:i:s');          // use oracle to_date function to format the date
        $modifytime = date('Y-m-d H:i:s');          // use oracle to_date function to format the date

        $sql="INSERT INTO COMPLAINT(COMPLAINTID,USE_USERID,COMPLAINT,VALID,TYPE,CREATETIME,MODIFYTIME) VALUES ('{$complaintid}','{$userid}','{$complaint}','{$valid}','{$type}',to_date('{$createtime}','yyyy-mm-dd hh24:mi:ss'),to_date('{$modifytime}','yyyy-mm-dd hh24:mi:ss'))";

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

// set timezone
date_default_timezone_set('UTC');

// global variable BASEDIR
define('BASEDIR',__DIR__);
include BASEDIR . '/Common/Loader.php';

// using PSR-0 coding standard
spl_autoload_register('\\Common\\Loader::autoload');

// check user post data
$check = new CommonAPI();
// $check->getFiles();
$check->check();

//print_r($check->params);
//echo "hello";

// connect database
try {
	// generate database handle
    $connect = Oracle::getInstance()->connect();
} catch (Exception $e) {
	throw new Exception("Database connection error: " . mysql_error());
}

// test data
$complaint['userid'] = "7fec24daf27dffbf18d188c7283bae58";
$complaint['complaint'] = "This is man is so nice!";

$ts = new SQL();
$ts->insertComplaint($connect,$complaint);


