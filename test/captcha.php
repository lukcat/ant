<?php 

$width = 100;
$length = 30;
$image = imagecreatetruecolor($width,$length);
$bgcolor = imagecolorallocate($image,255,255,255); //#ffffff 
imagefill($image,0,0,$bgcolor);

$dic = "abcdefghijkmnpqrstuvwxy3456789";

for($i=0;$i<4;$i++) {
    $fontsize = 6;
    $fontcolor = imagecolorallocate($image,rand(0,120),rand(0,120),rand(0,120));  //#000000
    $fontcontent = substr($dic, rand(0,strlen($dic)));

    $x = $i*20 + rand(0,5);
    $y = rand(5,10);

    imagestring($image, $fontsize, $x, $y, $fontcontent, $fontcolor);
}

header('content-type:image/png');

imagepng($image, );
