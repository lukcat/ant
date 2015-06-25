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
use App\Inquiry\Vehicle_Inquiry as Vehicle_Inquiry;
use App\Graphics\Mobile_Graphics as Mobile_Graphics;
use Common\Response as Response;

// global variable BASEDIR
define('BASEDIR',__DIR__);
include BASEDIR . '/Common/Loader.php';

// using PSR-0 coding standard
spl_autoload_register('\\Common\\Loader::autoload');

// check user post data
//$check = new CommonAPI();
//$check->check();

//$username = $check->params['username'];
//$password = $check->params['password'];

//$action = $check->params['action'];

try {
	// generate database handle
	$connect = Oracle::getInstance()->connect();
} catch (Exception $e) {
	throw new Exception("Database connection error: " . mysql_error());
}

// for test
$userInfo['loginname'] = 'cdq';
$userInfo['email'] = 'cdq@test.com';
$userInfo['cellphone'] = '12345678902';
$userInfo['name'] = 'chendeqing';
$userInfo['note'] = 'lanren2';
//$userInfo['token'] = '15a9cdeaf10a34e6a820c780e3cb2a6c';
//$userInfo['token'] = '';

$userInfo['password'] = sha1(md5('test'));

//$username = 'chendq';
//$password = sha1(md5('test'));

$action = 'Register';
//$action = 'Login';

// response user action 
switch($action) {
	case 'Login':
		// use App\Login\Mobile_Login class
		$ml = new Mobile_Login();
		// varify loginname and password
		$ml->login($userInfo, $connect);

		break;
	case 'Register':
		// use App\Register\Mobile_Register class
		$rg = new Mobile_Register();
		$rg->register($userInfo, $connect);

		break;
	case 'Upload':
		// use App\Upload\File_Upload class
		$fu = new File_Upload();
		// spacify file storage path
		$savePath = BASEDIR . "/uploads/";
		// save file and insert file information into database
		$fu->uploadFile($check->params, $connect);

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

