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
use App\UserInformation\User_Info as User_Info;
use App\UserInformation\User_ChangePWD as User_ChangePWD;
use App\UserInformation\User_ResetPWD as User_ResetPWD;
use App\Graphics\Image_Information as Image_Information;
use App\Mq\SendMessageToMq as SendMessageToMq;

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
    $antConnect    = Oracle::getInstance()->connect($configInfo['serverSet']['antServer']);
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

$check->params['complaint'] = 'shit';
$check->params['complaintid'] = '13e06c6f7ce8a1a1fdb361a147207894';
$check->params['serialnumber'] = 'GBI0142';
*/

/* register */
/* 
$check->params['loginname'] = 'chendeqing';
$check->params['password'] = sha1(md5('test'));
$check->params['icardid'] = '123321200010010908';
$check->params['email'] = 'chendeqing@ceiec.com.cn';
$check->params['cellphone'] = '12345678903';
$check->params['name'] = 'chendeqing';
$check->params['note'] = 'lanren2';
*/

/* bus */
//$check->params['countryid'] = '1';
//$check->params['cityid'] = '1';

/* complaint */
/*
$check->params['loginid'] = 'chendeqing@ceiec.com.cn';
$check->params['password'] = sha1(md5('test'));
$check->params['complaint'] = 'shit';
$check->params['complainttype'] = '1';
*/

/* get complaint */
/*
$check->params['loginid'] = 'huojing@ceiec.com.cn';
$check->params['password'] = sha1(md5('qwerty'));
$check->params['complaint'] = 'shit';
$check->params['complainttype'] = '1';
*/

/* inquiryVehicleByVehicleID */
/*
$check->params['vehicleid'] = 'GBI0142';
$check->params['querytype'] = 'vehicleid';
$check->params['loginid'] = 'chendeqing@ceiec.com.cn';
$check->params['password'] = sha1(md5('test'));
*/

/* inquiryVehicleByAntID */
/*
$check->params['antid'] = '09.2.61351.1';
$check->params['querytype'] = 'antid';
$check->params['loginid'] = 'chendeqing@ceiec.com.cn';
$check->params['password'] = sha1(md5('test'));
*/

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
//$userDataSet['action'] = 'GetComplaint';
//$userDataSet['action'] = 'InquiryVehicle';
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

// return password to user
//$testdata = array("password" => $userDataSet['password'], "loginname" => $userDataSet['loginname'], "action" => $userDataSet['action']);
//Response::show(0,"This is test message",$testdata);

// response user action 
$action = $userDataSet['action'];

switch($action) {
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

	case 'Register':
        // 5
		$rg = new Mobile_Register();
		$rg->register($userDataSet, $mobileConnect);

		break;

	case 'Complaint':
        //Varify user's indentity first
		$ml = new Mobile_Login();
        // login return a array which contains userid and token 
		$arrayInfo = $ml->login($userDataSet, $mobileConnect);
        $userid = $arrayInfo['userId'];

        // maybe useless
        if (empty($userid)) {
            Response::show(401,"User Login Failure");
        }

        // get complaint text
        $uc = new User_Complaint();
        //$complaintid = $uc->ReceiveComplaint($connect,$check->params, $userid);
        $complaintID = $uc->generateComplaintID();
        // Insert compaint text into database
        $complaintInfo = $uc->ReceiveComplaint($mobileConnect,$userDataSet, $userid, $complaintID);

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
            //var_dump($res);die();
            foreach($res as $imageInfo) {
                //var_dump($imageInfo);die();
                $info = $fu->insertPhotoInfo($mobileConnect,$imageInfo,$complaintID);
                //var_dump($info);die();
                array_push($infos, $info);
            }

            $ip = new Image_Processing();
            $ipRes = $ip->generateThumbnail($mobileConnect,$infos);
            //echo 'ipRes';
            //var_dump($ipRes);die();

            $ipInfos = array();
            foreach($ipRes as $imageInfo) {
                $info = $ip->insertthumbnailInfo($mobileConnect, $imageInfo, $complaintID);
                array_push($ipInfos, $info);
            }

        }


        $smtm = new SendMessageToMq($configInfo);
        //var_dump($configInfo);die();

        // Get message which will send to rabbitMq
        $mqMsg = $smtm->filterMessage($complaintInfo);

        // send message to mq
        $smtm->send($mqMsg);
        //die();

        Response::show(600,'Complaint message upload successful');

        break;

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

	case 'InquiryVehicle':

        // get vehicle's information
		$iv = new Vehicle_Inquiry();

        // get query type 
        $queryType = $userDataSet['querytype'];

        $resData = array();
        if ($queryType == 'vehicleid') {
		    $resData = $iv->getVehicleInfoByVehicleID($antConnect, $userDataSet['vehicleid']);
        } elseif ($queryType == 'antid') {
            $resData = $iv->getVehicleInfoByAntID($mobileConnect, $userDataSet['antid']);
        } else {
            Response::show(910,'queryType is not correct');
        }
        //var_dump($resData);die();

        /* send result to user's email box
         * use multiple process of php
         */
        if (!empty($userDataSet['token']) || !empty($userDataSet['loginid'])) {
            // get user's email address
            $ui = new User_Info();
            $emailaddr = $ui->getEmail($mobileConnect, $userDataSet);
            $body = json_encode($resData);

            //echo 'in userDataSet';
            // new child process
            $pid = pcntl_fork();    
            if ($pid == 0) {
                //echo 'in child';
                sleep(2);
                // In child process, do sending email job

                // get user's id
                if ($emailaddr) {
                    $mailer = new Mailer();
                    $mailer->sendmails($configInfo, $emailaddr, $body);
                }

                exit;
                break;

            } else {
                // This is main process, return result to client
                // response result to client
                if ($resData) {
                    Response::show(900,"Vehicle Exist", $resData);
                } else {
                    //Response::show(901,"Vehicle Do Not Exist",$testData);
                    Response::show(901,"Vehicle Do Not Exist");
                }

                break;

            }
        } else {
            if ($resData) {
                Response::show(900,"Vehicle Exist", $resData);
            } else {
                //Response::show(901,"Vehicle Do Not Exist",$testData);
                Response::show(901,"Vehicle Do Not Exist");
            }

            break;

        }

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
        //Response::show(1,"test",$userDataSet);

        $uf = new User_ResetPWD();

        // get basic information
        $resData = $uf->getSecurityCode($mobileConnect, $userDataSet);

        // Get parameters
        //$loginid = $resData['loginid'];
        $email = $resData['email'];
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

	default:
		// no action matches
        //$data = array('code' => 0, 'msg' => 'No action spacified');
		Response::show(20,"Default Message: No action specified");

		break;
}


