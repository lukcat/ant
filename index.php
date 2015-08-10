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
use Common\Get_Config as Get_Config;

// global variable BASEDIR
define('BASEDIR',__DIR__);
include BASEDIR . '/Common/Loader.php';

// using PSR-0 coding standard
spl_autoload_register('\\Common\\Loader::autoload');

// check user post data
$check = new CommonAPI();
$check->check();


// get configure data, including hostname, $instance, $user and $password
$configPath = './config/config';

//echo realpath($configPath);
//if (file_exists($configPath)) {
//    echo "file exists";
//}

$getConfig = new Get_Config($configPath);
if (!$getConfig->readConfig()) {
    exit('read configure file failure');
}

// Get parameters from xml
$hostname = $getConfig->hostname;
$instance = $getConfig->instance;
$username = $getConfig->username;
$password = $getConfig->password;

$rootPath = $hostname . '/ant';

// connect database
try {
	// generate database handle
    //$connect = Oracle::getInstance()->connect();
    $connect = Oracle::getInstance()->connect($hostname,$instance,$username,$password);
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
$check->params['complaintid'] = '13e06c6f7ce8a1a1fdb361a147207894';
$check->params['vehicleid'] = 'GBI0142';
//$username = 'chendq';
//$password = sha1(md5('test'));

$userDataSet = $check->params;
//$userDataSet['complaintid'] = kkkk
$action = 'InquiryVehicle';
//$action = 'DeleteComplaint';
//$action = 'GetComplaint';
//$action = 'Complaint';
//$action = 'Register';
//$action = 'Login';
//exit;

///////////////end of test////////////////////////////////////

// response user action 
switch($action) {
	case 'Login':
        // 4
		$ml = new Mobile_Login();
		// varify loginname and password
		$ml->login($userDataSet, $connect);

		break;

	case 'Register':
        // 5
		$rg = new Mobile_Register();
		$rg->register($userDataSet, $connect);

		break;

	case 'Complaint':
        //Varify user's indentity first
		$ml = new Mobile_Login();
		$userid = $ml->login($userDataSet, $connect);

        // get complaint
        $uc = new User_Complaint();
        //$complaintid = $uc->ReceiveComplaint($connect,$check->params, $userid);
        $complaintid = $uc->ReceiveComplaint($connect,$userDataSet, $userid);

        // get files
        //$files = $check->params['files'];
        $files = $userDataSet['files'];

        /* 
         * Upload files 
         */
        if (!empty($files)) {
            $fu = new File_Upload();
            $resData = array();
            $res = $fu->uploadFile($files);

            // Insert infomation into database
            // $res contains file basic information, include file's localname, photoid etc.
            $infos = array();
            foreach($res as $imageInfo) {
                $info = $fu->insertPhotoInfo($connect,$imageInfo,$complaintid);
                array_push($infos, $info);

            }

            $ip = new Image_Processing();
            $ipRes = $ip->generateThumbnail($connect,$infos);
            $ipInfos = array();
            foreach($ipRes as $imageInfo) {
                $info = $ip->insertthumbnailInfo($connect, $imageInfo, $complaintid);
                array_push($ipInfos, $info);
            }

        }
        Response::show(6,'File message',$res);

        break;

    case 'GetComplaint':
        //Varify user's indentity first
		$ml = new Mobile_Login();
		//$userid = $ml->login($check->params, $connect);
		$userid = $ml->login($userDataSet, $connect);

        // Get user complaint
        $uc = new User_Complaint();
        $res = $uc->GetComplaint($connect, $userid, $rootPath);
        //var_dump($res);

        Response::show(700,"Get User's Complaint Successful",$res);

        break;

    case 'DeleteComplaint':
        //Varify user's indentity first
		$ml = new Mobile_Login();
		$userid = $ml->login($userDataSet, $connect);

        $dc = new User_complaint();
        $res = $dc->deleteComplaint($connect, $userDataSet);
        if ($res) {
            //$data = array('code' => 1, 'msg' => 'Successful');
            Response::show(800,"Delete Complaint Information Successful");
            
        } else {
            //$data = array('code' => 0, 'msg' => 'Failure', $data);
            Response::show(801,"Delete Complaint Information Failure");
        }

        break;

	case 'InquiryVehicle':
		$iv = new Vehicle_Inquiry();
		$resData = $iv->getVehicleInfo($connect, $userDataSet['vehicleid']);
        if ($resData) {
		    Response::show(900,"Vehicle Exist", $resData);
        } else {
		    Response::show(901,"Vehicle Do Not Exist");
            //$reData = array('code' => 0, 'msg' => 'Vehicle not exist');
        }

		break;

	default:
		// no action matches
        $data = array('code' => 0, 'msg' => 'No action spacified');
		Response::show(3,"Default Message", $data);

		break;
}


