<?php 

$width = 80;
$height= 30;
$image = imagecreatetruecolor($width,$height);
$bgcolor = imagecolorallocate($image,255,255,255); //#ffffff 
imagefill($image,0,0,$bgcolor);

$dic = "abcdefghijkmnpqrstuvwxy3456789";

// generate font
$fontNumber = 4;
for($i=0;$i<$fontNumber;$i++) {
    $fontsize = 5;
    $fontcolor = imagecolorallocate($image,rand(0,120),rand(0,120),rand(0,120));  //#000000
    $fontcontent = substr($dic, rand(0,strlen($dic)-1),1);

    $x = $i*($width/$fontNumber)+ rand(5,10);
    $y = rand(5,10);

    imagestring($image, $fontsize, $x, $y, $fontcontent, $fontcolor);
}

// Add point
$pointNumber = 100;
for ($i=0; $i<$pointNumber; $i++) {
    $pointColor = imagecolorallocate($iamge, rand(120,200),rand(120,200),rand(120,200));
    $x = rand(0,$width);
    $y = rand(0,$height);

    imagesetpixel($image, $x, $y, $pointColor);
}

// Add line
$lineNumber = 5;
for ($i=0; $i<$lineNumber; $i++) {
    $lineColor = imagecolorallocate($iamge, rand(120,200),rand(120,200),rand(120,200));
    $startX = rand(0,$width);
    $startY = rand(0,$height);
    $endX   = rand(0,$width);
    $endY   = rand(0,$height);

    imageline($image,$startX,$startY,$endX,$endY,$lineColor);
}

header('content-type:image/png');

imagepng($image);

