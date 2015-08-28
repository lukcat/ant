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
use App\Bus\BusInformation as BusInformation;
use Common\Response as Response;
use Common\Get_Config as Get_Config;
use Common\Config as Config;
use Common\PHPMailer\Mailer as Mailer;

// global variable BASEDIR
define('BASEDIR',__DIR__);
include BASEDIR . '/Common/Loader.php';
include BASEDIR . '/Common/PHPMailer/PHPMailer.php';

// using PSR-0 coding standard
spl_autoload_register('\\Common\\Loader::autoload');

// check user post data
$check = new CommonAPI();
$check->check();


// get configure data, including hostname, $instance, $user and $password
$configFile = './config/sys_config.xml';

//echo realpath($configPath);
//if (file_exists($configPath)) {
//    echo "file exists";
//}

//new config
$cfg = new Config();

$configInfo = $cfg->getXml($configFile);


//var_dump($configInfo);
//exit;

///////////////////test data///////////////////
/// out of date
$configPath = './config/config';

$getConfig = new Get_Config($configPath);
if (!$getConfig->readConfig()) {
    exit('read configure file failure');
}

// Get parameters from xml
$hostname = $getConfig->hostname;
$instance = $getConfig->instance;
$username = $getConfig->username;
$password = $getConfig->password;
///////////////////test data///////////////////

$hostname = $configInfo['host'];
$rootPath = $hostname . '/ant';

///////////test mail//////////////////
/*
$address = 'chendeqing@ceiec.com.cn';
$body = 'This is a test from chendq';

$mailer = new Mailer();
$mailer->sendmails($configInfo, $address, $body);
*/
//exit;

//////////end test of mail//////////////

// connect database
try {
	// generate database handle
    //$connect = Oracle::getInstance()->connect();
    //$connect = Oracle::getInstance()->connect($hostname,$instance,$username,$password);
    // two connect instance
    // one for ant, another for mobile
    $antConnect    = Oracle::getInstance()->connect($configInfo['serverSet']['antServer']);
    $mobileConnect = Oracle::getInstance()->connect($configInfo['serverSet']['mobileServer']);

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

//////////////////////////////
// useful test data
//////////////////////////////
/* dumingjun
$check->params['loginname'] = 'aaaaa';
$check->params['password'] = sha1(md5('aaaaa'));
*/

$check->params['loginname'] = 'cdq';
$check->params['password'] = sha1(md5('test'));
$check->params['email'] = 'cdq@test.com';
$check->params['cellphone'] = '12345678902';
$check->params['name'] = 'chendeqing';
$check->params['note'] = 'lanren2';

$check->params['complaint'] = 'shit';
$check->params['complaintid'] = '13e06c6f7ce8a1a1fdb361a147207894';
$check->params['vehicleid'] = 'GBI0142';
/*
*/
////////////////end of test data//////////////////////


// insert user parameters into userDataSet
$userDataSet = $check->params;
//$username = 'chendq';
//$password = sha1(md5('test'));
//$password = sha1(md5('test',true));
$userDataSet['cityname'] = 'beijing';
//$userDataSet['action'] = 'InquiryBus';
//$userDataSet['action'] = 'GetComplaint';
//$userDataSet['action'] = 'Register';
//$userDataSet['action'] = 'Complaint';
//$userDataSet['action'] = 'GetComplaint';
//$userDataSet['action'] = 'InquiryVehicle';
//$userDataSet['action'] = 'Login';

// return password to user
//$testdata = array("password" => $userDataSet['password'], "loginname" => $userDataSet['loginname'], "action" => $userDataSet['action']);
//Response::show(0,"This is test message",$testdata);

// response user action 
$action = $userDataSet['action'];
switch($action) {
	case 'Login':
        // 4
		$ml = new Mobile_Login();
		// varify loginname and password
		$userid = $ml->login($userDataSet, $mobileConnect);
        if (!empty($userid)) {
            Response::show(400,"User Login Successful");
        } else {
            Response::show(401,"User Login Failure");
        }

		break;

	case 'Register':
        // 5
		$rg = new Mobile_Register();
		$rg->register($userDataSet, $mobileConnect);

		break;

	case 'Complaint':
        //Varify user's indentity first
		$ml = new Mobile_Login();
		$userid = $ml->login($userDataSet, $mobileConnect);
        if (empty($userid)) {
            Response::show(401,"User Login Failure");
        }

        // get complaint
        $uc = new User_Complaint();
        //$complaintid = $uc->ReceiveComplaint($connect,$check->params, $userid);
        $complaintid = $uc->ReceiveComplaint($mobileConnect,$userDataSet, $userid);

        // get files
        //$files = $check->params['files'];
        $files = $userDataSet['files'];

        /* 
         * Upload files 
         */
        $res = array();
        if (!empty($files)) {
            $fu = new File_Upload();
            $resData = array();
            $res = $fu->uploadFile($files);

            // Insert infomation into database
            // $res contains file basic information, include file's localname, photoid etc.
            $infos = array();
            foreach($res as $imageInfo) {
                $info = $fu->insertPhotoInfo($mobileConnect,$imageInfo,$complaintid);
                array_push($infos, $info);

            }

            $ip = new Image_Processing();
            $ipRes = $ip->generateThumbnail($mobileConnect,$infos);
            $ipInfos = array();
            foreach($ipRes as $imageInfo) {
                $info = $ip->insertthumbnailInfo($mobileConnect, $imageInfo, $complaintid);
                array_push($ipInfos, $info);
            }

        }
        Response::show(6,'Complaint message',$res);

        break;

    case 'GetComplaint':
        //Varify user's indentity first
		$ml = new Mobile_Login();
		//$userid = $ml->login($check->params, $connect);
		$userid = $ml->login($userDataSet, $mobileConnect);

        // Get user complaint
        $uc = new User_Complaint();
        $res = $uc->GetComplaint($mobileConnect, $userid, $rootPath);
        //var_dump($res);

        if ($res) {
            Response::show(700,"Get User's Complaint Successful",$res);
        } else {
            Response::show(701,"Get User's Complaint Error");
        }

        break;

    case 'DeleteComplaint':
        //Varify user's indentity first
		$ml = new Mobile_Login();
		$userid = $ml->login($userDataSet, $mobileConnect);

        $dc = new User_complaint();
        $res = $dc->deleteComplaint($mobileConnect, $userDataSet);
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
		$resData = $iv->getVehicleInfo($antConnect, $userDataSet['vehicleid']);
        // send email to user????

        if ($resData) {
		    Response::show(900,"Vehicle Exist", $resData);
        } else {
		    //Response::show(901,"Vehicle Do Not Exist",$testData);
		    Response::show(901,"Vehicle Do Not Exist");
        }

		break;

    case 'InquiryBus':
        // Get bus information
        $ib = new BusInformation();
        $busInfo = $ib->getBusInformation($mobileConnect, $userDataSet['cityname']);
        if ($busInfo) {
            Response::show(300,"Bus Basic Information",$busInfo);
        } else {
            Response::show(301,"No Bus In City {$userDataSet['cityname']}");
        }

        break;

	default:
		// no action matches
        $data = array('code' => 0, 'msg' => 'No action spacified');
		Response::show(2,"Default Message", $data);

		break;
}


