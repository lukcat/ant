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
use App\Graphics\Mobile_Graphics as Mobile_Graphics;
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

//$loginname = $check->params['loginname'] = 'cdq';
//$check->params['email'] = 'cdq@test.com';
//$check->params['cellphone'] = '12345678902';
//$check->params['name'] = 'chendeqing';
//$check->params['note'] = 'lanren2';
//$check->params['password'] = sha1(md5('test'));
//$username = 'chendq';
//$password = sha1(md5('test'));

//echo $loginname;

$action = 'Upload';
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
	case 'Upload':
        //echo "upload";
        //Varify user's indentity first
		//$ml = new Mobile_Login();
		//$ml->login($check->params, $connect);

        // get files
        //echo "print params in index";
        $files = $check->params['files'];
        //print_r($files);
        //print_r($check->params['files']);
        //if ($files == '') {
        //    Response::show(720,'No files uploaded');
        //}

        // upload file
		$fu = new File_Upload();
        $resData = array();
        foreach($files as $fileInfo) {
            print_r($fileInfo);
            $res = $fu->uploadFile($fileInfo);
            print_r($res);
            if ($res) {
                $filename = $fileInfo['name'];
                $code = $res['code'];
                $msg = $res['message'];
                
                $data = array(
                        'code' => $code,
                        'filename' => $filename,
                        'msg' => $msg,
                        );
                
                // add to response data
                $resData[$filename] = $data;

            }
            // TODO
            // response upload result
            // Response::show(code,msg,data);
            // 7 represent file 
        }
        Response::show(7,'File message',$resData);

		// spacify file storage path
		//$savePath = BASEDIR . "/uploads/";

		// save file and insert file information into database
		//$fu->uploadFile($check->params, $connect);

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


