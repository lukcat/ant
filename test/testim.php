<?php 

//header('Content-type: image/jpeg');

function getExt($filename){
    return strtolower(pathinfo($filename,PATHINFO_EXTENSION));
} 

function getUniName(){
    return md5(uniqid(microtime(true),true));
}

$imagePath = realpath('image.jpg');

//$image = new Imagick('image.jpg');
$image = new Imagick($imagePath);

// If 0 is provided as a width or height parameter,
// aspect ratio is maintained
$image->thumbnailImage(100, 100);
//$image->thumbnailImage(50, 0);

$savePath = "./first/second";
$realpath = realpath($savePath);

if(!file_exists($realpath)){
    if(!mkdir($path,0777,true)) {
        return false;
    }
    chmod($path,0777);
}

$ext=getExt($imagePath);
$uniName = getUniName();
$destination = $realpath.'/'.$uniName.'.'.$ext;
//echo $destination;

//copy($image->getImageBlob(),$destination);
$image->writeImage($destination);

//header('Content-type: image/jpeg');
//echo $image->getImageBlob();



