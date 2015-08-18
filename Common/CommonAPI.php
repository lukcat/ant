<?php
/*
 * CommonAPI.php
 * Description: this is common module, check/save user data 
 *  Created on: 2015/4/21
 *      Author: Chen Deqing
 */

namespace Common;

class CommonAPI {
	public $params;
    public $app;
    //private $files;

    protected function getFiles(){
        $files = '';
        $i=0;
        foreach($_FILES as $file){
            if(is_string($file['name'])){
                $files[$i]=$file;
                $i++;
            }elseif(is_array($file['name'])){
                foreach($file['name'] as $key=>$val){
                    $files[$i]['name']=$file['name'][$key];
                    $files[$i]['type']=$file['type'][$key];
                    $files[$i]['tmp_name']=$file['tmp_name'][$key];
                    $files[$i]['error']=$file['error'][$key];
                    $files[$i]['size']=$file['size'][$key];
                    $i++;
                }
            }
        }
        //return $files;
        $this->params['files'] = $files;
    }

    protected function getUsers() {
		$this->params['loginname'] = $loginname = isset($_POST['loginname']) ? $_POST['loginname'] : '';
        $this->params['email'] = $email = isset($_POST['email']) ? $_POST['email'] : '';
		$this->params['cellphone'] = $cellphone = isset($_POST['cellphone']) ? $_POST['cellphone'] : '';
		$this->params['password'] = $password = isset($_POST['password']) ? sha1(md5($_POST['password'],true)) : '';
		$this->params['name'] = $name = isset($_POST['name']) ? $_POST['name'] : '';
		$this->params['note'] = $note = isset($_POST['note']) ? $_POST['note'] : '';
		// use md5 and sha1 to encrypt user password
		$this->params['token'] = $token = isset($_POST['token']) ? $_POST['token'] : '';
    }

    protected function getComplaint() {
        $this->params['complaint'] = $complaint = isset($_POST['complaint']) ? $_POST['complaint'] : '';
    }

    protected function getComplaintID() {
        $this->params['complaintid'] = $complaint = isset($_POST['complaintid']) ? $_POST['complaintid'] : '';
    }

    protected function getCityName() {
        $this->params['cityname'] = $cityname = isset($POST['cityname']) ? $POST['cityname'] : '';
    }

    protected function getUserAction() {
		$this->params['action'] = $action = isset($_POST['action']) ? $_POST['action'] : '';
    }

    protected function getVehicleID() {
		$this->params['vehicleid'] = $vehicleid = isset($_POST['vehicleid']) ? $_POST['vehicleid'] : '';
    }

    public function check() {
		/*************
         * User action
         */
		//$this->params['action'] = $action = isset($_POST['action']) ? $_POST['action'] : '';
        $this->getUserAction();
        
		/*************
         * City name 
         */
        $this->getCityName();

		/*****************
         * User infomation
         */
        $this->getUsers();

		/*************
         * Complaint text
         */
        $this->getComplaint();
        
		/*************
         * Complaint ID
         */
        $this->getComplaintID();

		/*********************
         * Vehicle information
         */
		//$this->params['vehicleid'] = $vehicleid = isset($_POST['vehicleid']) ? $_POST['vehicleid'] : '';
        $this->getVehicleID();

		/******************
         * File information
         */
        $this->getFiles();
        //$this->params['files'] = $this->getFiles();
        //echo "print params in common";
        //var_dump($this->params['files']);

        ///////
        ///old version
        /*
		$fileInfo = $_FILES;
		$id = key($fileInfo); // in test, $id = 'myfile'
		$myfile = $fileInfo[$id];
		//var_dump($myfile);
		$this->params['filename'] = $filename = isset($myfile['name']) ? $myfile['name'] : '';
		$this->params['filetmpname'] = $filetmpname = isset($myfile['tmp_name']) ? $myfile['tmp_name'] : '';
		$this->params['filetype'] = $filetype = isset($myfile['type']) ? $myfile['type'] : '';
		$this->params['filesize'] = $filesize = isset($myfile['size']) ? $myfile['size'] : '';
		$this->params['fileerror'] = $fileerror = isset($myfile['error']) ? $myfile['error'] : '';
        */

        /**
         * Debug program
         */
		//$test_value = 'filename: '.$filename.' '.'filetmpname'.$filetmpname.' '.'filetype'.$filetype.' '.'filesize'.$filesize. ' '.$fileerror;
		//$info = file_get_contents('php://input');

		//////////////////// write log ////////////////
		// The new person to add to the file
		//$info = date('l dS \of F Y h:i:s A') . ":: action->" . $action . ", username->" . $username . ", password->" . $password . "POST->" . implode('',$_SERVER)."\n";
		//$info = ":: action->" . $action . ", username->" . $username . ", password->" . $password . "\n";
		//$info = $vehicleid . ' ' . $action;
		// Write the contents to the file, 
		// using the FILE_APPEND flag to append the content to the end of the file
		// and the LOCK_EX flag to prevent anyone else writing to the file at the same time
		//$file = BASEDIR . '/log/CommonAPI_log.txt';
		//$info = $test_value;
		//file_put_contents($file, $info, FILE_APPEND | LOCK_EX);
	}
}

