<?php
// by chendq 2015/5/4

namespace App\Upload;

use Common\Response as Response;

class SingleUpload {
	
	//$fileInfo = $_FILES['myFile'];
	function uploadFile($fileInfo, $connect, $uploadPath = 'uploads', $flag=true, $allowExt=array('gif','png','jpeg','jpg','bmp')) {
		
		//判断错误号
		 if($fileInfo['error'] > 0 ) {
			 switch ($fileInfo['error']) {
				 case 1:
					// 上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值
					Response::show(701, 'uploaded file exceeds upload_max_filesize whick defined in php.ini');
						 break;
				 case 2:
					// 上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值
					Response::show(702, 'uploaded file exceeds MAX_FILE_SIZE which defined in client');
						 break;
				 case 3:
					// 文件只有部分被上传
					Response::show(703, 'file was partially uploaded');
					break;
				 case 4:
					//没有文件被上传
					Response::show(704, 'No file uploaded');
					break;
				 case 6:
					// 找不到临时文件夹
					Response::show(706, 'temporary folder can not be find');
					break;
				 case 7:
					// 文件写入失败'
					Response::show(707, 'file write failure');
					break;
			 }
		}
		
		// 得到文件扩展名
		$ext = pathinfo($fileInfo['name'],PATHINFO_EXTENSION);
		//$allowExt = array('jpeg', 'jpg', 'png', 'gif');
		
		// 检测上传类型
		if (!in_array($ext, $allowExt)) {
			Response::show(709, '非法文件类型');
		}
		 
		// 检测上传文件大小
		$maxSize = 20971520; // 单位字节，20M
		if ($fileInfo['size'] > $maxSize) {
			Response::show(710, '上传文件过大');
		}
	
		// Get image size
		if ($flag) {
			if (getimagesize($fileInfo['tmp_name')) {
				Response::show(713, 'File is not image');
			}
		}


		// 判断是否HTTP POST方式上传
		if (!is_uploaded_file(fileInfo['tmp_name']) {
			Response::show(711, '非HTTP POST方式上传'];
		}
		
		// 保存文件
		//$uploadPath = 'uploads';
		if (!file_exist($uploadPath)) {
			mkdir($uploadPath, 0777, true);
			chmod($uploadPath, 0777);
		}
		$uniName = md5(uniqid($username,true),true).'.'.$ext;
		$destination = $uploadPath.'/'.$uniName;
		if (!@move_uploaded_file($fileInfo['tmp_name'], $destination)) {
			Response::show(712, '文件保存失败');
		}
		
		Response::show(700, '文件上传成功');
	}
}
