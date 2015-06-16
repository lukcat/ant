<?php 

// use aliases
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

/*
echo md5(rasmuslerdorf);

echo "<hr/>";

echo Crypt(rasmuslerdorf, '$1$rasmusle$');

echo "<hr/>";

echo Crypt(rasmuslerdorf);
*/

//echo phpinfo();

