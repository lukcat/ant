<?php 

date_default_timezone_set('Asia/Chongqing');

// use aliases
/*
use Common\Db as Db;
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

$gm = new Mobile_Graphics();
$gm->generate_thumbnail();

//echo phpinfo();
//$fileInfo = $_FILES;
//print_r($_FILES);

//echo "hello world";

//echo key(rasmuslerdorf$fileInfo);
*/

/*
echo md5(rasmuslerdorf);

echo "<hr/>";

echo Crypt(rasmuslerdorf, '$1$rasmusle$');

echo "<hr/>";

echo Crypt(rasmuslerdorf);
*/

//echo phpinfo();

// test xml 
//$file = './config/config';
//if (file_exists($file)) {
//    $xml = simplexml_load_file($file);
//    //print_r($xml);
//    print_r((string)$xml->hostname);
//    print_r((string)$xml->instance);
//    /*
//    foreach($xml as $item) {
//        //print_r($item);
//        print_r((string)$item);
//    } */
//    //var_dump($xml['myString']);
//}


//$testarr = Array('aa'=>'test');
//
//$var = isset($testarr['bb']) ? $testarr['bb'] : 'kong';
//
//echo $var;

//echo strftime("%S",time());

$arr1 = array('attr11'=>11, 'atrr12'=>12);
$arr2 = array('attr1'=>21, 'atrr22'=>22);

$resdata = array($arr1, $arr2);

var_dump($resdata);

$num = rand(0,1);
//var_dump($num);
printf($num);
unset($resdata[$num]);
var_dump($resdata);
//var_dump($resdata2);
