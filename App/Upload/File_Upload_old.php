<?php 
/*
 * File_Upload.php
 * Description: process single file uploads
 *  Created on: 2015/4/29
 *      Author: Chen Deqing
 */

/**
 * @params: CommonAPI接收数据
 * @savePath：指定存储路径
 * @connect: 数据库链接句柄
 * @checkFlag: true检查上传文件是否图片类型
 * @allowExt: 允许上传文件格式
 * @maxSize: 自定义最大文件大小，与php.ini中的设置同时起作用
 */

namespace App\Upload;

// use Common\Response equals to use Common\Response as Response
use Common\Response as Response;

class File_Upload {
	// process error 
	protected function processErr($params, $checkFlag=false, $allowExt=array('bmp','jpg','tiff','gif','pcx','tga','exif','fpx','svg','psd','cdr','pcd','dxf','ufo','eps','ai','raw'), $maxSize) {

		// default value of fileerror is empty string 
		if (is_string($params['fileerror'])) {	
			Response::show(714, 'No file uploaded');
		}
		if($params['fileerror'] > 0 ) {
			 switch ($params['fileerror']) {
				 case 1:
					// Uploaded file exceeds upload_max_filesize whick defined in php.ini
					Response::show(701, 'Uploaded file exceeds upload_max_filesize whick defined in php.ini');
						 break;
				 case 2:
					// Uploaded file excessds MAX_FILE_SIZE which defined in client
					Response::show(702, 'Uploaded file exceeds MAX_FILE_SIZE which defined in client');
						 break;
				 case 3:
					// File was partially uploaded
					Response::show(703, 'File was partially uploaded');
					break;
				 case 4:
					// No file uploaded
					Response::show(704, 'No file uploaded');
					break;
				 case 6:
					// Temporary folder can not be found
					Response::show(706, 'temporary folder can not be found');
					break;
				 case 7:
					// File write failure
					Response::show(707, 'File write failure');
					break;
			 }
		} else {
			// check file size
			if ($params['filesize'] > $maxSize) {
				Response::show(708, 'file uploaded is too large');
			}

			// check filename 
			$ext = strtolower(pathinfo($params['filename'],PATHINFO_EXTENSION));
			if (!in_array($ext, $allowExt)) {
				Response::show(709, 'illegal file type');
			}

			// check file type 
			if ($checkFlag) {
				if (!getimagesize($params['filetmpname'])) {
					Response::show(710, 'File is not image');
				}
			}
		}

	}

	public function uploadFile($params, $connect, $savePath='uploads', $checkFlag=true, $allowExt=array('jpg','jpeg','png','gif','bmp'), $maxSize=52428800) {

		// check whether storage folder is exist
		if (!file_exists($savePath)) {
			mkdir($savePath,0777,true);
			chmod($savePath,0777);
		}

		// check error
		$this->processErr($params, $checkFlag, $allowExt, $maxSize);

		// formate data of params for insert
		$username ='"'.$params['username'].'"';
		$originname = '"'.$params['filename'].'"';
		$filetmpname = '"'.$params['filetmpname'].'"';
		$filetype = '"'.$params['filetype'].'"';
		$filesize = '"'.$params['filesize'].'"';
		$description = '"'."this is description".'"';

		// ensure imageid and file local name is unique
		$imageid = '"'.$params['username']. date('Y/m/d-H:i:s') . 'R' . rand(). $params['filename'] . '"';
		$localname = '"'.$params['username']. date('YmdHis') . 'R' . rand(). $params['filename'] . '"';

		// get real path
		$realSavePath = realpath($savePath);

		// for database value
		$filepath = '"' . $realSavePath . '/' . trim($localname,'"') . '"';

		$fileerror = $params['fileerror'];

		$field = "imageid, originname, localname, type, path, size, description";
		$value = $imageid . ',' . $originname . ',' . $localname . ',' . $filetype . ',' . $filepath . ',' . $filesize . ',' .$description;

		// query sentance
		$insert_sql = 'insert into file ('.$field.') values ('.$value.')';

		// generate destination
		$destination = $realSavePath . '/' . trim($localname,'"');
		if(move_uploaded_file($params['filetmpname'], $destination)) {
			if (!$result = mysql_query($insert_sql, $connect)) {
				// query error occur
				Response::show(501,'File_Upload: query database by name error');
			}
			// upload OK
			Response::show(700,'File uploaded successful');
		} else {
			// move_uploaded_file error occur
			Response::show(711, 'File storage failure');
		}
	}
}

