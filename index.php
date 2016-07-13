<?php
/*
 * Index.php
 * Description: This is program main entrance, according to user action and calling relevant module
 *  Created on: 2015/4/10
 *      Author: Chen Deqing
 */

// set timezone
//date_default_timezone_set('UTC');
//date_default_timezone_set('ECT');
date_default_timezone_set('America/New_York');
// use aliases
use Common\Oracle as Oracle;
use Common\CommonAPI as CommonAPI;
use App\Login\Mobile_Login as Mobile_Login;
use App\Register\Mobile_Register as Mobile_Register;
use App\Upload\File_Upload as File_Upload;
use App\Inquiry\Vehicle_Inquiry as Vehicle_Inquiry;
use App\Graphics\Image_Processing as Image_Processing;
use App\Complaint\User_Complaint as User_Complaint;
use App\Bus\BusInformation as BusInformation;
use Common\Response as Response;
use Common\Get_Config as Get_Config;
use Common\Config as Config;
use Common\PHPMailer\Mailer as Mailer;
use App\UserInformation\User_Info as User_Info;
use App\UserInformation\User_ChangePWD as User_ChangePWD;
use App\UserInformation\User_ResetPWD as User_ResetPWD;
use App\Graphics\Image_Information as Image_Information;
use App\Mq\SendMessageToMq as SendMessageToMq;
use App\VehicleHistoryRoute\VehicleBasicInformation as VehicleBasicInformation;
use Common\Guid as Guid;
use App\Mq\MessageLog as MessageLog;
use App\Register\SM_Register as SM_Register;
use App\Complaint\SM_Complaint as SM_Complaint;

// for mq
//use PhpAmqpLib\Connection\AMQPStreamConnection;
//use PhpAmqpLib\Message\AMQPMessage;

// global variable BASEDIR
define('BASEDIR',__DIR__);
include BASEDIR . '/Common/Loader.php';
include BASEDIR . '/Common/PHPMailer/PHPMailer.php';

// using PSR-0 coding standard
spl_autoload_register('\\Common\\Loader::autoload');
//spl_autoload_register(['\\Common\\Loader','autoload'],true,true);

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
exit;
*/

//////////end test of mail//////////////

// connect database
try {
	// generate database handle
    //$connect = Oracle::getInstance()->connect();
    //$connect = Oracle::getInstance()->connect($hostname,$instance,$username,$password);
    // two connect instance
    // one for ant, another for mobile
    //print_r($configInfo['serverSet']['mobileServer']);
    //die();
    //$antConnect    = Oracle::getInstance()->connect($configInfo['serverSet']['antServer']);
    $mobileConnect = Oracle::getInstance()->connect($configInfo['serverSet']['mobileServer']);

} catch (Exception $e) {
	throw new Exception("Database connection error: " . mysql_error());
}

//echo "print params in index check";
//Response::show(1,"mesaage",$check->params['loginname']);
//$username = $check->params['username'];
//$password = $check->params['password'];

//$action = $check->params['action'];
//$check->params['loginid'] = 'chendeqing@ceiec.com.cn';
//$check->params['loginname'] = 'cdq';
//$check->params['email'] = 'chendeqing@ceiec.com.cn';
//$check->params['email'] = 'huojing@ceiec.com.cn';
//$check->params['cellphone'] = '12345678902';
//$check->params['name'] = 'chendeqing';
//$check->params['note'] = 'lanren2';
//$check->params['password'] = sha1(md5('test'));

//////////////////////////////
// useful test data
//////////////////////////////
/* dumingjun
$check->params['loginname'] = 'aaaaa';
$check->params['password'] = sha1(md5('aaaaa'));
*/

/*
$check->params['email'] = 'chendeqing@ceiec.com.cn';
$check->params['securitycode'] = '9894';
$check->params['sn'] = '56fafed1bf23e729879be9c618769a5ea78fb34f';
$check->params['timestamp'] = '2015-09-19 02:46:28';
$check->params['newpassword'] = sha1(md5('test1'));
*/

//$check->params['loginid'] = 'chendeqing@ceiec.com.cn';
//$check->params['securitycode'] = '7724';
//$check->params['newpassword'] = sha1(md5('test'));
//$check->params['sn'] = 'e67bd4f23672ad2ca4d45d1a27381dc7852b88ca';
//$check->params['loginname'] = 'dq';
//$check->params['password'] = sha1(md5('qwerty'));
//$check->params['password'] = sha1(md5('123456'));
//$check->params['originphotoid'] = '883d40b07426a53d99318505f0ef1bc3';
//$check->params['token'] = 'f0e3cdf2d5e16684c9fff48f379c5fb2';
//$check->params['loginid'] = 'dq';
//$check->params['securitycode'] = '7724';
//$check->params['loginid'] = 'chendeqing@ceiec.com.cn';
//$check->params['loginid'] = '12345678902';
/*
$check->params['newpassword'] = sha1(md5('test'));
$check->params['sn'] = 'e67bd4f23672ad2ca4d45d1a27381dc7852b88ca';
$check->params['loginname'] = 'dq';
$check->params['password'] = sha1(md5('test'));
$check->params['icardid'] = '123321200010010908';
$check->params['email'] = 'chendeqing@ceiec.com.cn';
$check->params['email'] = 'lukcatchen@126.com';
$check->params['cellphone'] = '12345678903';
$check->params['name'] = 'chendeqing';
$check->params['note'] = 'lanren2';

$check->params['complaint'] = 'complaint content here';
$check->params['complaintid'] = '13e06c6f7ce8a1a1fdb361a147207894';
$check->params['serialnumber'] = 'GBI0142';
*/

/* Login */
/* 
$check->params['password'] = sha1(md5('qwerty'));
$check->params['loginid'] = 'chendeqing@ceiec.com.cn';
*/

/* Register */
/* 
$check->params['loginname'] = 'lukcatchen';
$check->params['password'] = sha1(md5('test'));
$check->params['icardid'] = '0906393079';
$check->params['email'] = 'lukcatchen@126.com';
$check->params['cellphone'] = '12345678909';
$check->params['name'] = 'chendeqing';
$check->params['note'] = 'no note';
*/

/* ChangePWD*/
/* 
$check->params['loginid'] = 'chendeqing@ceiec.com.cn';
$check->params['password'] = sha1(md5('test'));
$check->params['newpassword'] = sha1(md5('test'));
*/

/* bus */
//$check->params['countryid'] = '1';
//$check->params['cityid'] = '1';

/* Complaint */
/*
$check->params['loginid'] = 'chendeqing@ceiec.com.cn';
$check->params['password'] = sha1(md5('qwerty'));
$check->params['complaint'] = 'complaint content here';
$check->params['complainttype'] = '1';
*/

/* get complaint */
/*
$check->params['loginid'] = 'huojing@ceiec.com.cn';
$check->params['password'] = sha1(md5('qwerty'));
//$check->params['complainttype'] = '1';
*/

/* Send vehicle information */
/*
$check->params['vehicleid'] = 'OAA1425';
$check->params['querytype'] = 'vehicleid';
$check->params['loginid'] = 'chendeqing@ceiec.com.cn';
$check->params['password'] = sha1(md5('test'));
//$check->params['loginid'] = '805002495@qq.com';
//$check->params['password'] = sha1(md5('qwerty'));
*/

/* InquiryVehicleByVehicleID */
/*
$check->params['vehicleid'] = 'EBA1440';
$check->params['querytype'] = 'vehicleid';
$check->params['loginid'] = 'chendeqing@ceiec.com.cn';
$check->params['password'] = sha1(md5('test'));
*/

/* InquiryVehicleByAntID */
//$check->params['antid'] = '09.2.61351.1';
/*
$check->params['antid'] = '09.2.61351.1';
$check->params['querytype'] = 'antid';
$check->params['loginid'] = 'chendeqing@ceiec.com.cn';
$check->params['password'] = sha1(md5('test'));
*/

/* GetBusLineInformation */
//$check->params['cityid'] = '1';

/* GetSecurityCode */
//$check->params['email'] = 'chendeqing@ceiec.com.cn';

/* GetVehicleList */
//$check->params['companyid'] = 'f64f20bc-e7ad-45ac-e043-1f021aac284c';

/* SMComplaint */
//$check->params['action'] = 'SMComplaint';
//$check->params['type'] = 'denunciar';
//$check->params['smguid'] = '123232334';
//$check->params['cellphone'] = '1234567891';
//$check->params['vehicleid'] = 'ASDF1223';
//$check->params['complaint'] = 'This is complaint';

////////////////end of test data//////////////////////


// insert user parameters into userDataSet
$userDataSet = $check->params;
//$username = 'chendq';
//$password = sha1(md5('test'));
//$password = sha1(md5('test',true));
//$userDataSet['cityname'] = 'beijing';
//$userDataSet['action'] = 'InquiryBus';
//$userDataSet['action'] = 'GetComplaint';
//$userDataSet['action'] = 'Register';
//$userDataSet['action'] = 'Complaint';
//$userDataSet['action'] = 'SMComplaint';
//$userDataSet['action'] = 'GetComplaint';
//$userDataSet['action'] = 'InquiryVehicle';
//$userDataSet['action'] = 'SendVehicleInformation';
//$userDataSet['action'] = 'Login';
//$userDataSet['action'] = 'ChangePWD';
//$userDataSet['action'] = 'GetUserInfo';
//$userDataSet['action'] = 'GetSecurityCode';
//$userDataSet['action'] = 'VerifySecurityCode';
//$userDataSet['action'] = 'ResetPassword';
//$userDataSet['action'] = 'GetOriginPhoto';
//$userDataSet['action'] = 'GetCountryVersion';
//$userDataSet['action'] = 'GetCityVersion';
//$userDataSet['action'] = 'GetCityInformation';
//$userDataSet['action'] = 'GetBusLineInformation';
//$userDataSet['action'] = 'testMQ';
//$userDataSet['action'] = 'GetBusGPS';
//$userDataSet['action'] = 'GetTaxiGPS';
//$userDataSet['action'] = 'GetCompanyList';
//$userDataSet['action'] = 'GetVehicleList';

// return password to user
//$testdata = array("password" => $userDataSet['password'], "loginname" => $userDataSet['loginname'], "action" => $userDataSet['action']);
//Response::show(0,"This is test message",$testdata);

// response user action 
$action = $userDataSet['action'];

switch($action) {
    // User login
	case 'Login':
        // 4
		// verify loginname and password
		$ml = new Mobile_Login();

        // Return a array contains userid and token
		$arrayInfo = $ml->login($userDataSet, $mobileConnect);

        // Get userid 
        //$loginid = $arrayInfo['loginId'];
        
        // Response information to client
        //if (!empty($loginid)) {

        Response::show(400,"User Login Successful",$arrayInfo);

        //} else {
            //Response::show(401,"User Login Failure");
        //}

		break;

    // User Register
	case 'Register':
        // 5
        // This part need modified
		$rg = new Mobile_Register();
		$rg->register($userDataSet, $mobileConnect);

		break;

    // User Complaint, including text and photos
	case 'Complaint':
        //Varify user's indentity first
		$ml = new Mobile_Login();
        // login return a array which contains userid and token 
		$userInfo = $ml->login($userDataSet, $mobileConnect);
        $userid = $userInfo['userId'];
        $userName = $userInfo['userName'];

        // maybe useless
        if (empty($userid)) {
            Response::show(401,"User Login Failure");
        }

        // get complaint text
        $uc = new User_Complaint();
        //$complaintid = $uc->ReceiveComplaint($connect,$check->params, $userid);
        $complaintID = $uc->generateComplaintID($mobileConnect);
        // Insert compaint text into database
        //$complaintInfo = $uc->ReceiveComplaint($mobileConnect,$userDataSet, $userInfo, $complaintID);
        //var_dump($complaintInfo);die();

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

            foreach ($res as $item) {
                if ($item['code'] != 0) {
                    // something wrong
                    Response::show(601,'uploads photo failure',$res);
                }
            }

            //// get complaint text
            //$uc = new User_Complaint();
            //$complaintid = $uc->ReceiveComplaint($connect,$check->params, $userid);
            //$complaintid = $uc->ReceiveComplaint($mobileConnect,$userDataSet, $userid);

            // Insert infomation into database
            // $res contains file basic information, include file's localname, photoid etc.
            $infos = array();
            foreach($res as $imageInfo) {
                $info = $fu->insertPhotoInfo($mobileConnect,$imageInfo,$complaintID);
                array_push($infos, $info);
            }

            // save thumbnail image
            $ip = new Image_Processing();
            $ipRes = $ip->generateThumbnail($mobileConnect,$infos);

            $ipInfos = array();
            foreach($ipRes as $imageInfo) {
                $info = $ip->insertThumbnailInfo($mobileConnect, $imageInfo, $complaintID);
                array_push($ipInfos, $info);
            }

        }

        // Insert compaint text into database
        $complaintInfo = $uc->ReceiveComplaint($mobileConnect,$userDataSet, $userInfo, $complaintID);

        /* Send message to RabbitMq*/
        // new a rabbitMq sender
        $smtm = new SendMessageToMq($configInfo);

        // Get message which will send to rabbitMq
        $mqMsg = $smtm->filterMessage($complaintInfo);

        // send message to mq
        $result = $smtm->send($mqMsg);
        
        /* End of Send message to RabbitMq*/

        /* Write message into database */
        // generate guid
        $gu = new Guid();
        $guid = $gu->generateGuid();

        // Insert message into database
        $ml = new MessageLog();
        if($ml->writeMessageLog($mobileConnect, $guid, $complaintID)) {
            // TODO
        } else {
            // TODO
        }
        /* End of write message into database */

        // Response message to client
        Response::show(600,'Complaint message upload successful');

        break;

	case 'SMComplaint':
        
        // insert user information to MAPP_USER
        $cellphone = $userDataSet['cellphone'];
        $vehicleid = $userDataSet['vehicleid'];
        $complaint = $userDataSet['complaint'];

        $sr = new SM_Register();
        $userid = $sr->getUseridByCellphone($cellphone, $mobileConnect);
        //echo $userid;die();

        $sc = new SM_Complaint();
        $res = $sc->ReceiveComplaint($mobileConnect, $userid, $vehicleid, $complaint);
        if ($res) {
            Response::show(2400,'Complaint message upload successful');
        }

        break;

    // Get user Complaint information
    case 'GetComplaint':
        //Varify user's indentity first
		$ml = new Mobile_Login();

        // login return a array which contains userid and token 
		$arrayInfo = $ml->login($userDataSet, $mobileConnect);
        $userid = $arrayInfo['userId'];

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

    // Delete user's complaint, not use yet
    case 'DeleteComplaint':
        //Varify user's indentity first
		$ml = new Mobile_Login();
		//$userid = $ml->login($userDataSet, $mobileConnect);

        // login return a array which contains userid and token 
		$arrayInfo = $ml->login($userDataSet, $mobileConnect);
        $userid = $arrayInfo['userId'];

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

    // Inquiry vehicle information
	case 'InquiryVehicle':

        // get vehicle's information
		$iv = new Vehicle_Inquiry();

        // get query type 
        $queryType = $userDataSet['querytype'];

        $resData = array();
        if ($queryType == 'vehicleid') {
		    $resData = $iv->getVehicleInfoByVehicleID($mobileConnect, $userDataSet['vehicleid']);
		    //$resData = $iv->getVehicleInfoByVehicleID($antConnect, $userDataSet['vehicleid']);
        } elseif ($queryType == 'antid') {
            $resData = $iv->getVehicleInfoByAntID($mobileConnect, $userDataSet['antid']);
        } else {
            Response::show(910,'queryType is not correct');
        }
        //var_dump($resData);die();

        /* send result to user's email box
         * use multiple process of php
         */
        //if (!empty($userDataSet['token']) || !empty($userDataSet['loginid'])) {
            // get user's email address
            $ui = new User_Info();
            $emailaddr = $ui->getEmail($mobileConnect, $userDataSet);
            $body = json_encode($resData);

            if ($resData) {
                //var_dump($resData);
                Response::show(900,"Vehicle Exist", $resData);
            } else {
                //Response::show(901,"Vehicle Do Not Exist",$testData);
                Response::show(901,"Vehicle Do Not Exist");
            }

            //if ($emailaddr) {
            //    $mailer = new Mailer();
            //    $mailer->sendmails($configInfo, $emailaddr, $body);
            //}

            break;

    // Inquiry vehicle information
	case 'SMInquiryVehicle':

        // get vehicle's information
		$iv = new Vehicle_Inquiry();

        // get query type 
        $queryType = $userDataSet['querytype'];

        $resData = array();
        if ($queryType == 'vehicleid') {
		    $resData = $iv->getVehicleInfoByVehicleID($mobileConnect, $userDataSet['vehicleid']);
		    //$resData = $iv->getVehicleInfoByVehicleID($antConnect, $userDataSet['vehicleid']);
        } elseif ($queryType == 'antid') {
            $resData = $iv->getVehicleInfoByAntID($mobileConnect, $userDataSet['antid']);
        } else {
            Response::show(2510,'queryType is not correct');
        }
        //var_dump($resData);die();

        /* send result to user's email box
         * use multiple process of php
         */
        //if (!empty($userDataSet['token']) || !empty($userDataSet['loginid'])) {
            // get user's email address
            $ui = new User_Info();
            $emailaddr = $ui->getEmail($mobileConnect, $userDataSet);
            $body = json_encode($resData);

            if ($resData) {
                //var_dump($resData);
                Response::show(2500,"Vehicle Exist", $resData);
            } else {
                //Response::show(901,"Vehicle Do Not Exist",$testData);
                Response::show(2501,"Vehicle Do Not Exist");
            }


            break;

    // Inquery user information and send to user's email
	case 'SendVehicleInformation':

        // get vehicle's information
		$iv = new Vehicle_Inquiry();

        // get query type 
        $queryType = $userDataSet['querytype'];

        $resData = array();
        if ($queryType == 'vehicleid') {
		    $resData = $iv->getVehicleInfoByVehicleID($mobileConnect, $userDataSet['vehicleid']);
        } elseif ($queryType == 'antid') {
            $resData = $iv->getVehicleInfoByAntID($mobileConnect, $userDataSet['antid']);
        }
        //var_dump($resData);die();

        /* send result to user's email box
         * use multiple process of php
         */
        if (!empty($userDataSet['token']) || !empty($userDataSet['loginid'])) {
            // get user's email address
            $ui = new User_Info();
            $emailaddr = $ui->getEmail($mobileConnect, $userDataSet);

            if (!empty($resData)) {
                //// get user's email address
                $body = json_encode($resData);

                //var_dump($resData);
                $vehicleType = $resData['vehicleType'];
                switch ($vehicleType) {
                    case '1':
                        $resData['vehicleType'] = 'Taxi'; 
                        break;

                    case '2':
                        $resData['vehicleType'] = 'Bus'; 
                        break;

                    case '3':
                        $resData['vehicleType'] = 'Bus de larga distancia'; 
                        break;

                    case '4':
                        $resData['vehicleType'] = 'Ambulancia'; 
                        break;

                    case '5':
                        $resData['vehicleType'] = 'Bus intraprovincial'; 
                        break;

                    default:
                        $resData['vehicleType'] = 'notypedata'; // no type data
                        break;
                }

                if ($resData['onlineFlag'] = '0') {
                    $resData['onlineFlag'] = 'En linea';
                } else {
                    $resData['onlineFlag'] = 'fuera de linea';
                }

                // Table formate
                $body = "<table border='1'> <tr> <td colspan='2' align='center' > Vehicle Information </td> </tr> <tr> <td> Estado </td> <td> {$resData['onlineFlag']} </td> </tr> <tr> <td> Placa </td> <td> {$resData['vehicleid']} </td> </tr> <tr> <td> ANT Kit SN  </td> <td> {$resData['antSN']} </td> </tr>  <tr> <td> Tipo de vehiculo  </td> <td> {$resData['vehicleType']} </td> </tr> <tr> <td> Compania  </td> <td> {$resData['company']} </td> </tr> <tr> <td> Dueno  </td> <td> {$resData['owner']} </td> </tr> <tr> <td> Marca  </td> <td> {$resData['brandModel']} </td> </tr> <tr> <td> Distrito </td> <td> {$resData['district']} </td> </tr> </tr> <tr> <td> Fecha de instalacion </td> <td> {$resData['installationFinishTime']} </td> </tr></table>";

                if ($emailaddr) {
                    //echo $body;
                    $mailer = new Mailer();
                    //var_dump($configInfo);
                    //echo $emailaddr;
                    //$emailaddr = 'chendeqing@ceiec.com.cn';
                    //echo $emailaddr;
                    $mailer->sendmails($configInfo, $emailaddr, $body);
                    Response::show(1900,"Send email successful");
                }

                Response::show(1901,"Send email failure");
            } else {
                // offline case
                $body = "<table border='1'> <tr> <td colspan='2' align='center' > Vehicle Information </td> </tr> <tr> <td> Estado </td> <td> Fuera de linea </td> </tr> <tr> <td> Placa </td> <td> {$userDataSet['vehicleid']} </td> </tr></table>";

                if ($emailaddr) {
                    //echo $body;
                    $mailer = new Mailer();
                    $mailer->sendmails($configInfo, $emailaddr, $body);
                    Response::show(1900,"Send email successful");
                }

                Response::show(1901,"Send email failure");
            }
        } 

        Response::show(1902,"Send email failure: NO user basic information");

        break;

    case 'InquiryBus':
        // Get bus information
        $ib = new BusInformation();
        $busInfo = $ib->getBusInformation($mobileConnect, $userDataSet['cityname']);
        if ($busInfo) {
            Response::show(300,"Get bus basic information successful",$busInfo);
        } else {
            Response::show(301,"No Bus In City {$userDataSet['cityname']}");
        }

        break;

    case 'ChangePWD':
        //  13
        $um = new User_ChangePWD();

        // modify password
        $um->changePassword($mobileConnect, $userDataSet);

        break;

    case 'GetUserInfo':
        // 1
        $ui = new User_Info();

        // Get user's basic information
        $ui->getUserBasicInfo($mobileConnect, $userDataSet);

        break;

    case 'GetSecurityCode':
        // 10

        //echo 'here';die();
        $uf = new User_ResetPWD();

        // get basic information
        $resData = $uf->getSecurityCode($mobileConnect, $userDataSet);

        // Verify user accout
        $email = $resData['email'];
        //$uf->verifyEmail($mobileConnect, $email);
        
        // Get parameters
        //$loginid = $resData['loginid'];
        $securitycode = $resData['securitycode'];
        $sn = $resData['sn'];
        $timestamp = $resData['timestamp'];

        // sent security code to user's email address
        //$responseData = array('loginid' => $loginid, 'email' => $email, 'sn' => $sn);
        $responseData = array('email' => $email, 'sn' => $sn, 'timestamp'=> $timestamp);

        //$body = json_encode($emailcontent);
        $body = "Scurity Code is:{$securitycode}";

        if (!empty($email)) {
            $mailer = new Mailer();
            $mailer->sendmails($configInfo, $email, $body);
        } else {
            Response::show(1001, "Send mail failure");
        }

        // response serial number, loginid and email to client
        Response::show(1000, 'Get security code successful', $responseData);

        break;

    case 'VerifySecurityCode':
        // 12
        $uf = new User_ResetPWD();

        $uf->verifySecurityCode($userDataSet);

        break;

    // params
    case 'ResetPassword':
        // 11
        $uf = new User_ResetPWD();

        $uf->changePwdBySecurityCode($mobileConnect, $userDataSet);

        break;

    case 'GetOriginPhoto':
        $ii = new Image_Information();

        $originPhotoID = $userDataSet['originphotoid'];

        $ii->getOriginPhoto($mobileConnect, $originPhotoID, $rootPath);

        break;

    case 'GetCountryVersion':
        $cv = new BusInformation();

        $countryID = $userDataSet['countryid'];
        $countryVersionNum = $cv->getCountryVersion($mobileConnect, $countryID);

        Response::show(1500, 'Get country version number successful', $countryVersionNum);

        break;

    case 'GetCityVersion':
        $cv = new BusInformation();

        $cityID = $userDataSet['cityid'];
        $cityVersionNum = $cv->getCityVersion($mobileConnect, $cityID);

        Response::show(1600, 'Get city version number successful', $cityVersionNum);

        break;

    case 'GetCityInformation':
        $cv = new BusInformation();

        //$cityID = $userDataSet['cityid'];
        $cityInformation = $cv->getCityInformation($mobileConnect);

        Response::show(1700, 'Get city list successful', $cityInformation);

        break;

    case 'GetBusLineInformation':
        $cv = new BusInformation();

        $cityID = $userDataSet['cityid'];
        $busLineInformation = $cv->getBusLineInformation($mobileConnect, $cityID);

        Response::show(1800, 'Get city list successful', $busLineInformation);

        break;

    case 'testMQ':

        $conmq = new AMQPStreamConnection('172.18.8.13',5672,'guest','guest');
        $channel = $conmq->channel();
        
        $channel->queue_declare('mobile',false,true,false,false);
        //$channel->exchange_declare('MB_EXCHANGE', 'topic', true, false, false);
        $bindKey='MB.V2.RP.12345';
        $channel->queue_bind('mobile','MB_EXCHANGE', $bindKey);
        
        $jsondata = array('ComplaintId'=>'12345', 'UserId'=>'user110', 'ComplaintType'=>'type1', 'CreateTime'=>'2016-01-06 17:38:00');
        
        $jsondata_str = json_encode($jsondata);
        $msg = new AMQPMessage($jsondata_str);
        
        $channel->basic_publish($msg, 'MB_EXCHANGE', $bindKey);
        
        $channel->close();
        $conmq->close();

        break;

    case 'GetBusGPS':
        // basic test data
        $longitude = array( -79.864969, -79.865145, -79.865431, -79.866343, -79.866692, -79.867073, -79.867722, -79.867990, -79.869068, -79.870892, -79.876713, -79.877698, -79.878176, -79.878398, -79.878543, -79.878527, -79.878345, -79.877723, -79.877317, -79.877050, -79.876889, -79.876704, -79.876548, -79.876433, -79.876462, -79.876556, -79.876629, -79.876886);
        $latitude = array(-2.150012,-2.150752,-2.150993,-2.151331,-2.151883,-2.152162,-2.152291,-2.152296,-2.152457,-2.152929,-2.154333,-2.154649,-2.154574,-2.154398,-2.154135,-2.153787,-2.153353,-2.152565,-2.151886,-2.151383,-2.150945,-2.150358,-2.149677,-2.148810,-2.147966,-2.146633,-2.146246,-2.145361);
        $speed = array(20,30,40,50,60);
        $direction = array(1,10,20,50,90,100,120,150,180,270,350);
        $vehicleid = array('GSX1001','QET2346','ASD2432','FDJ3464');

        // random index 
        $gpsIndexGroup1 = rand(0,6);   
        $gpsIndexGroup2 = rand(7,13);   
        $gpsIndexGroup3 = rand(14,20);   
        $gpsIndexGroup4 = rand(21,27);    

        
        // assign data
        
        // 1#vehicle
        $speedIndex = rand(0,4);
        $directionIndex = rand(0,10);
        //$vehicleidIndex = rand(0,3);

        $longitude1 = $longitude[$gpsIndexGroup1];
        $latitude1 = $latitude[$gpsIndexGroup1];
        $speed1 = $speed[$speedIndex];
        $direction1 = $direction[$directionIndex];
        $vehicleid1 = $vehicleid[1];

        // 2# vehicle
        $speedIndex = rand(0,4);
        $directionIndex = rand(0,10);

        $longitude2 = $longitude[$gpsIndexGroup2];
        $latitude2 = $latitude[$gpsIndexGroup2];
        $speed2 = $speed[$speedIndex];
        $direction2 = $direction[$directionIndex];
        $vehicleid2 = $vehicleid[2];

        // 3# vehicle
        $speedIndex = rand(0,4);
        $directionIndex = rand(0,10);

        $longitude3 = $longitude[$gpsIndexGroup3];
        $latitude3 = $latitude[$gpsIndexGroup3];
        $speed3 = $speed[$speedIndex];
        $direction3 = $direction[$directionIndex];
        $vehicleid3 = $vehicleid[3];

        // 4# vehicle
        $speedIndex = rand(0,4);
        $directionIndex = rand(0,10);

        $longitude4 = $longitude[$gpsIndexGroup4];
        $latitude4 = $latitude[$gpsIndexGroup4];
        $speed4 = $speed[$speedIndex];
        $direction4 = $direction[$directionIndex];
        $vehicleid4 = $vehicleid[4];

        $vehicleInfo1 = array('longitude'=>$longitude1, 'latitude'=>$latitude1, 'vehicleID'=>$vehicleid1, 'speed'=>$speed1, 'direction'=>$direction1);
        $vehicleInfo2 = array('longitude'=>$longitude2, 'latitude'=>$latitude2, 'vehicleID'=>$vehicleid2, 'speed'=>$speed2, 'direction'=>$direction2);
        $vehicleInfo3 = array('longitude'=>$longitude3, 'latitude'=>$latitude3, 'vehicleID'=>$vehicleid3, 'speed'=>$speed3, 'direction'=>$direction3);
        $vehicleInfo4 = array('longitude'=>$longitude4, 'latitude'=>$latitude4, 'vehicleID'=>$vehicleid4, 'speed'=>$speed4, 'direction'=>$direction4);

        $resData = array();
        array_push($resData, $vehicleInfo1);
        array_push($resData, $vehicleInfo2);
        array_push($resData, $vehicleInfo3);
        array_push($resData, $vehicleInfo4);

        $deleteIndex = rand(0,3);
        array_splice($resData,$deleteIndex,1);

        Response::show(2000, 'Get bus GPS successful', $resData);
        break;

    case 'GetTaxiGPS':
        // basic test data
        $longitude = array( -79.864969, -79.865145, -79.865431, -79.866343, -79.866692, -79.867073, -79.867722, -79.867990, -79.869068, -79.870892, -79.876713, -79.877698, -79.878176, -79.878398, -79.878543, -79.878527, -79.878345, -79.877723, -79.877317, -79.877050, -79.876889, -79.876704, -79.876548, -79.876433, -79.876462, -79.876556, -79.876629, -79.876886);
        $latitude = array(-2.150012,-2.150752,-2.150993,-2.151331,-2.151883,-2.152162,-2.152291,-2.152296,-2.152457,-2.152929,-2.154333,-2.154649,-2.154574,-2.154398,-2.154135,-2.153787,-2.153353,-2.152565,-2.151886,-2.151383,-2.150945,-2.150358,-2.149677,-2.148810,-2.147966,-2.146633,-2.146246,-2.145361);
        $speed = array(20,30,40,50,60);
        $direction = array(1,10,20,50,90,100,120,150,180,270,350);
        $vehicleid = array('GSX1001','QET2346','ASD2432','FDJ3464');

        // random index 
        $gpsIndexGroup1 = rand(0,6);   
        $gpsIndexGroup2 = rand(7,13);   
        $gpsIndexGroup3 = rand(14,20);   
        $gpsIndexGroup4 = rand(21,27);    

        
        // assign data
        
        // 1#vehicle
        $speedIndex = rand(0,4);
        $directionIndex = rand(0,10);
        //$vehicleidIndex = rand(0,3);

        $longitude1 = $longitude[$gpsIndexGroup1];
        $latitude1 = $latitude[$gpsIndexGroup1];
        $speed1 = $speed[$speedIndex];
        $direction1 = $direction[$directionIndex];
        $vehicleid1 = $vehicleid[1];

        $vehicleInfo1 = array('longitude'=>$longitude1, 'latitude'=>$latitude1, 'vehicleID'=>$vehicleid1, 'speed'=>$speed1, 'direction'=>$direction1);

        $resData = array();
        array_push($resData, $vehicleInfo1);

        //$deleteIndex = rand(0,3);
        //array_splice($resData,$deleteIndex,1);

        Response::show(2000, 'Get bus GPS successful', $resData);
        break;

    case 'GetCompanyList':
        $vbi = new VehicleBasicInformation();

        $resData = $vbi->getCompanyList($mobileConnect);
		Response::show(2200,"Get company list successful",$resData);

        break;

    case 'GetVehicleList':
        $vbi = new VehicleBasicInformation();

        $resData = $vbi->getVehicleList($mobileConnect, $check->params['companyid']);
        //$resData = $vbi->getVehicleList($mobileConnect);

		Response::show(2300,"Get vehicle list successful",$resData);
        break;

	default:
		// no action matches
        //$data = array('code' => 0, 'msg' => 'No action spacified');
		Response::show(20,"Default Message: No action specified");

		break;
}


