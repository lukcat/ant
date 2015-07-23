<?php
/*
 * Index.php
 * Description: This is program main entrance, according to user action and calling relevant module
 *  Created on: 2015/4/10
 *      Author: Chen Deqing
 */

// set timezone
date_default_timezone_set('UTC');

// use aliases
use Common\Oracle as Oracle;
use Common\CommonAPI as CommonAPI;
use App\Login\Mobile_Login as Mobile_Login;
use App\Register\Mobile_Register as Mobile_Register;
use App\Upload\File_Upload as File_Upload;
//use App\Upload\Graphic_Upload as Graphic_Upload;
use App\Inquiry\Vehicle_Inquiry as Vehicle_Inquiry;
//use App\Graphics\Mobile_Graphics as Mobile_Graphics;
use App\Graphics\Image_Processing as Image_Processing;
use App\Complaint\User_Complaint as User_Complaint;
use Common\Response as Response;

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

//////////////////////////////////////////////
///////////////// for test  ////////////////
/*
$sql = "select * from app_user";
$res = oci_parse($connect,$sql);
if(!oci_execute($res)) {
    echo "exit";
}
if ($testrows = oci_fetch_array($res, OCI_BOTH)) {
    echo $testrows['NAME'];
}
*/

//echo "print params in index check";
//Response::show(1,"mesaage",$check->params['loginname']);
//$username = $check->params['username'];
//$password = $check->params['password'];

//$action = $check->params['action'];

//$userInfo['loginname'] = 'cdq';
//$userInfo['email'] = 'cdq@test.com';
//$userInfo['cellphone'] = '12345678902';
//$userInfo['name'] = 'chendeqing';
//$userInfo['note'] = 'lanren2';
//$userInfo['password'] = sha1(md5('test'));

$check->params['loginname'] = 'cdq';
$check->params['email'] = 'cdq@test.com';
$check->params['cellphone'] = '12345678902';
$check->params['name'] = 'chendeqing';
$check->params['note'] = 'lanren2';
$check->params['password'] = sha1(md5('test'));

$check->params['complaint'] = 'shit';
//$username = 'chendq';
//$password = sha1(md5('test'));

//echo $loginname;

$action = 'GetComplaint';
//$action = 'Complaint';
//$action = 'Register';
//$action = 'Login';
//exit;

///////////////end of test////////////////////////////////////

// response user action 
switch($action) {
	case 'Login':
		// use App\Login\Mobile_Login class
		$ml = new Mobile_Login();
		// varify loginname and password
		$ml->login($check->params, $connect);

		break;
	case 'Register':
		// use App\Register\Mobile_Register class
		$rg = new Mobile_Register();
		$rg->register($check->params, $connect);

		break;
	case 'Complaint':
        //Varify user's indentity first
		$ml = new Mobile_Login();
		$userid = $ml->login($check->params, $connect);
        //echo "userid is:\n";
        //echo $userid;

        // get complaint
        $uc = new User_Complaint();
        $complaintid = $uc->ReceiveComplaint($connect,$check->params, $userid);

        // get files
        $files = $check->params['files'];

        /* 
         * Upload files 
         */
        if (!empty($files)) {
        //if (empty($files)) {
            $fu = new File_Upload();
            $resData = array();
            //foreach($files as $fileInfo) {
                /*
                 * Porcess origin photo
                 */
                $res = $fu->uploadFile($files);

                // Insert infomation into database
                // $res contains file basic information, include file's localname, photoid etc.
                $infos = array();
                foreach($res as $imageInfo) {
                    $info = $fu->insertPhotoInfo($connect,$imageInfo,$complaintid);
                    array_push($infos, $info);

                }

                //if (isset($imagePath)) {
                $ip = new Image_Processing();
                $ipRes = $ip->generateThumbnail($connect,$infos);
                $ipInfos = array();
                foreach($ipRes as $imageInfo) {
                    //var_dump($imageInfo);
                    //echo "\n";
                    $info = $ip->insertthumbnailInfo($connect, $imageInfo, $complaintid);
                    array_push($ipInfos, $info);
                }
                echo "ipInfos is: ";
                var_dump($ipInfos);
                echo "ipInfos ended";

                // Insert infomation into database(thumbnail)
                // $ipRes contains file basic information, include file's localname, photoid etc.




                //echo "ipRes is: ";
                //var_dump($ipRes);
                //echo "ipRes is ended";
                //echo "leave image";
                //}

                /*
                if ($res) {
                    //print_r($res);
                    $imagePath = (string)$res['data']['imageLocalName'];
                    
                    array_push($resData,$res);
                }
                
                $imagePath = "/var/www/html/ant/uploads/origin/761808130d2dcb61c46519a7344ae1f5.jpg";
                if (isset($imagePath)) {
                    $ip = new Image_Processing();

                    $ip->generateThumbnail($connect,realpath($imagePath));
                    echo "leave image";
                }
                */
            //}
        }
        // Analysis resData
        // If reData contains error, delete all other uploaded files
        // User should uploads all files again

        // TODO
        //Response::show(7,'File message',$resData);
        Response::show(7,'File message',$res);

		// spacify file storage path
		//$savePath = BASEDIR . "/uploads/";

		// save file and insert file information into database
		//$fu->uploadFile($check->params, $connect);

		break;
    case 'GetComplaint':
        //Varify user's indentity first
		$ml = new Mobile_Login();
		$userid = $ml->login($check->params, $connect);

        // Get user complaint
        $uc = new User_Complaint();
        $uc->GetComplaint($connect, $userid);

        break;
	case 'InquiryVehicle':
		$iv = new Vehicle_Inquiry();
		$iv->getVehicleInfo($check->params['vehicleid'], $connect);
		Response::show(1,"this is InquiryVehicle");

		break;
	default:
		// no action matches
		Response::show(601,"no action");

		break;
}


