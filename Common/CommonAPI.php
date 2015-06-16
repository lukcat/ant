<?php
// by chendq 2015/4/21
/*
 * CommonAPI.php
 * Description: this is common module, check/save user data 
 *  Created on: 2015/4/21
 *      Author: Chen Deqing
 */


namespace Common;
/**
 * 处理接口公共业务
 */

class CommonAPI {
	public $params;
	public $app;
	public function check() {
		// user action
		$this->params['action'] = $action = isset($_POST['action']) ? $_POST['action'] : '';

		// user login and register 
		$this->params['username'] = $username = isset($_POST['username']) ? $_POST['username'] : '';
		// use md5 and sha1 to encrypt user password
		$this->params['password'] = $password = isset($_POST['password']) ? sha1(md5($_POST['password'],true)) : '';
		$this->params['token'] = $token = isset($_POST['token']) ? $_POST['token'] : '';

		// for vehicle inquiry
		$this->params['vehicleid'] = $vehicleid = isset($_POST['vehicleid']) ? $_POST['vehicleid'] : '';

		// for single file upload 
		$fileInfo = $_FILES;
		$id = key($fileInfo); // in test, $id = 'myfile'
		$myfile = $fileInfo[$id];
		//var_dump($myfile);
		$this->params['filename'] = $filename = isset($myfile['name']) ? $myfile['name'] : '';
		$this->params['filetmpname'] = $filetmpname = isset($myfile['tmp_name']) ? $myfile['tmp_name'] : '';
		$this->params['filetype'] = $filetype = isset($myfile['type']) ? $myfile['type'] : '';
		$this->params['filesize'] = $filesize = isset($myfile['size']) ? $myfile['size'] : '';
		$this->params['fileerror'] = $fileerror = isset($myfile['error']) ? $myfile['error'] : '';

		$test_value = 'filename: '.$filename.' '.'filetmpname'.$filetmpname.' '.'filetype'.$filetype.' '.'filesize'.$filesize. ' '.$fileerror;
		//$info = file_get_contents('php://input');

		//////////////////// write log ////////////////
		// The new person to add to the file
		//$info = date('l dS \of F Y h:i:s A') . ":: action->" . $action . ", username->" . $username . ", password->" . $password . "POST->" . implode('',$_SERVER)."\n";
		//$info = ":: action->" . $action . ", username->" . $username . ", password->" . $password . "\n";
		//$info = $vehicleid . ' ' . $action;
		// Write the contents to the file, 
		// using the FILE_APPEND flag to append the content to the end of the file
		// and the LOCK_EX flag to prevent anyone else writing to the file at the same time
		$file = BASEDIR . '/log/CommonAPI_log.txt';
		$info = $test_value;
		file_put_contents($file, $info, FILE_APPEND | LOCK_EX);
	}
}

