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

        $sql="INSERT INTO COMPLAINT(COMPLAINT_ID,USER_ID,COMPLAINT,VALID,TYPE,CREATE_TIME,MODIFY_TIME) VALUES ('{$complaintid}','{$userid}','{$complaint}','{$valid}','{$type}',to_date('{$createtime}','yyyy-mm-dd hh24:mi:ss'),to_date('{$modifytime}','yyyy-mm-dd hh24:mi:ss'))";

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
    * Test result: This module is OK
    */
    public function insertPhotoInfo($connect,$imageInfo) {

        $photoid = md5(uniqid(microtime(true),true));   // primary key
        $complaintid = $imageInfo['complaintid'];       // foreign key
        $localname = $imageInfo['localname'];           // file name in local system
        $originname = $imageInfo['originname'];         // file origin name
        $size = $imageInfo['size'];                     // file size
        $type = $imageInfo['type'];                     // file type
        $valid = 1;                                     // 1 represent effective, 0 reprensent ineffective
        $path = $imageInfo['path'];                     // file's relative path
        $description = $imageInfo['description'];       // file description
        $createtime = date('Y-m-d H:i:s');              // file's create_time $modifytime = date('Y-m-d H:i:s');              // file's modify_time 
        $modifytime = date('Y-m-d H:i:s');              // file's create_time $modifytime = date('Y-m-d H:i:s');              // file's modify_time 


        $sql="INSERT INTO PHOTO(PHOTO_ID,COMPLAINT_ID,LOCAL_NAME,ORIGIN_NAME,PHOTO_SIZE,TYPE,VALID,PATH,DESCRIPTION,CREATE_TIME,MODIFY_TIME) VALUES ('{$photoid}','{$complaintid}','{$localname}','{$originname}',{$size},'{$type}',{$valid},'{$path}','{$description}',to_date('{$createtime}','yyyy-mm-dd hh24:mi:ss'),to_date('{$modifytime}','yyyy-mm-dd hh24:mi:ss'))";

        //echo $sql;

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
    * Test result: This module is OK
    */
    public function insertThumbnailInfo($connect, $thumbnailInfo) {

        $thumbnailid = md5(uniqid(microtime(true),true));   // primary key
        $photoid = $thumbnailInfo['photoid'];               // foreign key
        $complaintid = $thumbnailInfo['complaintid'];       // foreign key

        $localname = $thumbnailInfo['localname'];           // file name in local system
        $originname = $thumbnailInfo['originname'];         // file origin name
        $size = $thumbnailInfo['size'];                     // file size
        $type = $thumbnailInfo['type'];                     // file type
        $valid = 1;                                     // 1 represent effective, 0 reprensent ineffective
        $path = $thumbnailInfo['path'];                     // file's relative path
        $description = $thumbnailInfo['description'];       // file description
        $createtime = date('Y-m-d H:i:s');              // file's create_time
        $modifytime = date('Y-m-d H:i:s');              // file's modify_time 
        $sql="INSERT INTO THUMBNAIL(THUMBNAIL_ID,PHOTO_ID,COMPLAINT_ID,LOCAL_NAME,ORIGIN_NAME,PHOTO_SIZE,TYPE,VALID,PATH,DESCRIPTION,CREATE_TIME,MODIFY_TIME) VALUES ('{$thumbnailid}','{$photoid}','{$complaintid}','{$localname}','{$originname}',{$size},'{$type}',{$valid},'{$path}','{$description}',to_date('{$createtime}','yyyy-mm-dd hh24:mi:ss'),to_date('{$modifytime}','yyyy-mm-dd hh24:mi:ss'))";

        echo $sql;

        $stid = oci_parse($connect,$sql);

        if(!oci_execute($stid)) {
            return false;
        } else {
            return $photoid;
        }
        
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

// new object
$ts = new SQL();

/* 
 * For complaint table
 */
//$complaint['userid'] = "7fec24daf27dffbf18d188c7283bae58";
//$complaint['complaint'] = "This is man is so nice!";

$ts->insertComplaint($connect,$complaint);
/*****************************************************/

/*
 * For photo table
 */
//$photoInfo['complaintid'] = '7fec24daf27dffbf18d188c7283bae58';
//$photoInfo['localname'] = 'localname';
//$photoInfo['originname'] = 'originname';
//$photoInfo['size'] = 1024;
//$photoInfo['type'] = 'JPG';
//$photoInfo['valid'] = 1;
//$photoInfo['path'] = './uploads';
//$photoInfo['description'] = 'This is beautiful';
//
//$ts->insertPhotoInfo($connect,$photoInfo);
/**************************************************/


/*
 * For thumbnail table
 */

$photoInfo['complaintid'] = '7fec24daf27dffbf18d188c7283bae58';
$photoInfo['photoid'] = '7fec24daf27dffbf18d188c7283bae58';
$photoInfo['localname'] = 'localname';
$photoInfo['originname'] = 'originname';
$photoInfo['size'] = 1024;
$photoInfo['type'] = 'JPG';
$photoInfo['valid'] = 1;
$photoInfo['path'] = './uploads';
$photoInfo['description'] = 'This is beautiful';

$ts->insertThumbnailInfo($connect,$photoInfo);

