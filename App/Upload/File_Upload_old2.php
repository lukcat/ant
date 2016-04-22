<?php 
/*
 * Graphic_Upload.php
 * Description: process multiple files uploads
 *  Created on: 2015/6/29
 *      Author: Chen Deqing
 */

namespace App\Upload;

use Common\Response as Response;

class File_Upload{
    protected $fileInfo;
	protected $maxSize;
	protected $allowMime;
	protected $allowExt;
	protected $uploadPath;
	protected $imgFlag;
	protected $fileInfo;
	protected $error;
	protected $ext;
	protected $connect;
	/**
	 * @param array $fileInfo
	 * @param string $connect
	 * @param string $uploadPath
	 * @param string $imgFlag
	 * @param number $maxSize
	 * @param array $allowExt
	 * @param array $allowMime
	 */
	public function __construct($fileInfo,$connect,$uploadPath='./uploads',$imgFlag=false,$maxSize=5242880,$allowExt=array('jpeg','jpg','png','gif','txt'),$allowMime=array('image/jpeg','image/png','image/gif')){
		$this->fileInfo=$fileInfo;
        $this->connect=$connect;
		$this->maxSize=$maxSize;
		$this->allowMime=$allowMime;
		$this->allowExt=$allowExt;
		$this->uploadPath=$uploadPath;
		$this->imgFlag=$imgFlag;
	}
	/**
	 * 检测上传文件是否出错
	 * @return boolean
	 */
	protected function checkError(){
		if(!is_null($this->fileInfo)){
			if($this->fileInfo['error']>0){
				switch($this->fileInfo['error']){
					case 1:
					    Response::show(701, 'Uploaded file exceeds upload_max_filesize whick defined in php.ini');
						//$this->error='超过了PHP配置文件中upload_max_filesize选项的值';
						break;
					case 2:
					    Response::show(702, 'Uploaded file exceeds MAX_FILE_SIZE which is defined in client');
						//$this->error='超过了表单中MAX_FILE_SIZE设置的值';
						break;
					case 3:
					    Response::show(703, 'File was partially uploaded');
						//$this->error='文件部分被上传';
						break;
					case 4:
					    Response::show(704, 'No file uploaded');
						//$this->error='没有选择上传文件';
						break;
					case 6:
					    Response::show(706, 'temporary folder can not be found');
						//$this->error='没有找到临时目录';
						break;
					case 7:
					    Response::show(707, 'File write failure');
						//$this->error='文件不可写';
						break;
					case 8:
					    Response::show(708, 'The upload interrupted by extensions');
						//$this->error='由于PHP的扩展程序中断文件上传';
						break;
						
				}
				return false;
			}else{
				return true;
			}
		}else{
            Response::show(709,'File upload error');
			//$this->error='文件上传出错';
			return false;
		}
	}
	/**
	 * 检测上传文件的大小
	 * @return boolean
	 */
	protected function checkSize(){
		if($this->fileInfo['size']>$this->maxSize){
			Response::show(710, 'file uploaded is too large');
			//$this->error='上传文件过大';
			return false;
		}
		return true;
	}
	/**
	 * 检测扩展名
	 * @return boolean
	 */
	protected function checkExt(){
		$this->ext=strtolower(pathinfo($this->fileInfo['name'],PATHINFO_EXTENSION));
		if(!in_array($this->ext,$this->allowExt)){
			Response::show(711, 'illegal file extension');
			//$this->error='不允许的扩展名';
			return false;
		}
		return true;
	}
	/**
	 * 检测文件的类型
	 * @return boolean
	 */
	protected function checkMime(){
        if ($this->imgFlag){
		    if(!in_array($this->fileInfo['type'],$this->allowMime)){
		    	Response::show(712, 'illegal file type');
		    	//$this->error='不允许的文件类型';
		    	return false;
		    }
		    return true;
        }
	}
	/**
	 * 检测是否是真实图片
	 * @return boolean
	 */
	protected function checkTrueImg(){
		if($this->imgFlag){
			if(!@getimagesize($this->fileInfo['tmp_name'])){
				Response::show(713, 'File is not image');
				//$this->error='不是真实图片';
				return false;
			}
			return true;
		}
	}
	/**
	 * 检测是否通过HTTP POST方式上传上来的
	 * @return boolean
	 */
	protected function checkHTTPPost(){
		if(!is_uploaded_file($this->fileInfo['tmp_name'])){
            Response::show(714, 'File is not uploaded by HTTP POST');
			//$this->error='文件不是通过HTTP POST方式上传上来的';
			return false;
		}
		return true;
	}
	/**
	 *显示错误 
	 */
	protected function showError(){
		exit('<span style="color:red">'.$this->error.'</span>');
	}
	/**
	 * 检测目录不存在则创建
	 */
	protected function checkUploadPath(){
		if(!file_exists($this->uploadPath)){
			if(!mkdir($this->uploadPath,0777,true)) {
                Response::show(715,'Upload path created failure');
            }
            chmod($this->uploadPath,0777);
		}
	}
	/**
	 * 产生唯一字符串
	 * @return string
	 */
	protected function getUniName(){
		return md5(uniqid(microtime(true),true));
	}
	/**
	 * 上传文件
	 * @return string
	 */
	public function uploadFile(){
		if($this->checkError()&&$this->checkSize()&&$this->checkExt()&&$this->checkMime()&&$this->checkTrueImg()&&$this->checkHTTPPost()){
			$this->checkUploadPath();
			$this->uniName=$this->getUniName();
			$this->destination=$this->uploadPath.'/'.$this->uniName.'.'.$this->ext;
			if(@move_uploaded_file($this->fileInfo['tmp_name'], $this->destination)){
				return  $this->destination;
			}else{
                Reponse::show(716,'move file error');
				//$this->error='文件移动失败';
				//$this->showError();
			}
		}
        //else{
		//	$this->showError();
		//}
	}
}

