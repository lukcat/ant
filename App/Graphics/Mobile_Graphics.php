<?php
// By chendq 2015/4/21

// namespace App\Graphics;

// use Common\Response;

class Mobile_Graphics {
	function test() {
		Response::show(000,'this is test message');
	}

	function save() {
		// read photo from client and save it to local system 
		// TODO
	}

	function generate_thumbnail($originName, $thumbnailName, $path, $width=255, $height=255) {
		// use third part toolkit to process photos
		// TODO
		// Instantiate a new Gmagick object
		$image = new Gmagick('/var/www/html/ant/uploads/mv.jpg');

		// Make thumbnail from image loaded. 0 for either axes preserves aspect ratio
		//$image->thumbnailImage(150, 0);
		$image->thumbnailImage($width, $height);

		// Create a border around the image, then simulate how the image will look like as an oil painting
		// Notice the chaining of mutator methods which is supported in gmagick
		// $image->borderImage("yellow", 8, 8)->oilPaintImage(0.3);

		// Write the current image at the current state to a file
		// $image->write('/var/www/html/ant/uploads/example_thumbnail.jpg');
		$image->write('example_thumbnail.jpg');
	}
}

$gm = new Mobile_Graphics();

$gm->generate_thumbnail();

