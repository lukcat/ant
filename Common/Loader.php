<?php

namespace Common;

class Loader {
	public static function autoload($class) {
		require BASEDIR .'/' . str_replace('\\', '/', $class) . '.php';
		//include BASEDIR .'/' . $class . '.php';
		//echo "loader";
		//var_dump($class);
	}
	static function test() {
		echo "This is Common Loader";
	}
}
