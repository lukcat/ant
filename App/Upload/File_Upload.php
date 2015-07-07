<?php 
/*
 * File_Upload.php
 * Description: process multiple files upload
 *  Created on: 2015/4/29
 *      Author: Chen Deqing
 */

namespace App\Upload;

class File_Upload {

    /**************************
     * Get file extension
     * @param string $filename
     * @return string
     */
    protected function getExt($filename){
    	return strtolower(pathinfo($filename,PATHINFO_EXTENSION));
    }

    /****************
     * Generate unique string 
     * @return string
     */
    function getUniName(){
        return md5(uniqid(microtime(true),true));
    }

    /**
     * Sigle-file / multiple single-file / multiple-files upload
     * @param array $fileInfo
     * @param string $path
     * @param string $flag
     * @param number $maxSize
     * @param array $allowExt
     * @return string
     */
    public function uploadFile($fileInfo,$path='./uploads',$flag=false,$maxSize=1048576,$allowExt=array('jpeg','jpg','png','gif','txt')){
        //$flag=true;
        //$allowExt=array('jpeg','jpg','gif','png');
        //$maxSize=1048576;//1M
        //判断错误号
        print_r($fileInfo);
        if($fileInfo['error']===UPLOAD_ERR_OK){
            //检测上传得到小
            if($fileInfo['size']>$maxSize){
                $res['code'] = 10;
                $res['message'] = $fileInfo['name'] . ' is too large';
                //$res['mes']=$fileInfo['name'].'上传文件过大';
                return $res;
            }
            $ext=$this->getExt($fileInfo['name']);
            //检测上传文件的文件类型
            if(!in_array($ext,$allowExt)){
                $res['code'] = 11;
                $res['message'] = $fileInfo['name'] . ' has illegal file extension';
                //$res['mes']=$fileInfo['name'].'非法文件类型';
                return $res;
            }
            //检测是否是真实的图片类型
            if($flag){
                if(!getimagesize($fileInfo['tmp_name'])){
                    $res['code'] = 12;
                    $res['message'] = $fileInfo['name'] . ' is not image';
                    //$res['mes']=$fileInfo['name'].'不是真实图片类型';
                    return $res;
                }

            }
            //检测文件是否是通过HTTP POST上传上来的
            if(!is_uploaded_file($fileInfo['tmp_name'])){
                $res['code'] = 14;
                $res['message'] = $fileInfo['name'] . ' is not uploaded by HTTP POST';
                //$res['mes']=$fileInfo['name'].'文件不是通过HTTP POST方式上传上来的';
                return $res;
            }
            //if($res) return $res;
            //$path='./uploads';
            if(!file_exists($path)){
                //echo "directory is not exist";
                if(!mkdir($path,0777,true)){
                    $res['code'] = 15;
                    $res['message'] = 'Create folder failure';
                    return $res;
                }
                chmod($path,0777);
            }
            $uniName=$this->getUniName();
            $destination=$path.'/'.$uniName.'.'.$ext;
            if(!move_uploaded_file($fileInfo['tmp_name'],$destination)){
                $res['code'] = 16;
                $res['message'] = 'move file:'.$fileInfo['name'].'error';
                //$res['mes']=$fileInfo['name'].'文件移动失败';
                return $res;
            }

            $res['code'] = 0;
            $res['message']= $fileInfo['name'].' uploads successful';
            //$res['mes']=$fileInfo['name'].'上传成功';
            //$res['dest']=$destination;
            return $res;
        }else{
            //匹配错误信息
            switch ($fileInfo['error']) {
                case 1 :
                    $res['code'] = 1;
                    $res['message'] = $fileInfo['name'].' exceeds upload_max_filesize which is defined in php.ini';
                    //$res['mes'] = '上传文件超过了PHP配置文件中upload_max_filesize选项的值';
                    break;
                case 2 :
                    $res['code'] = 2;
                    $res['message'] = $fileInfo['name'].' exceeds MAX_FILE_SIZE which is defined in client';
                    //$res['mes'] = '超过了表单MAX_FILE_SIZE限制的大小';
                    break;
                case 3 :
                    $res['code'] = 3;
                    $res['message'] = $fileInfo['name'].' is partially uploaded';
                    //$res['mes'] = '文件部分被上传';
                    break;
                case 4 :
                    $res['code'] = 4;
                    $res['message'] = 'No file uploaded';
                    //$res['mes'] = '没有选择上传文件';
                    break;
                case 6 :
                    $res['code'] = 6;
                    $res['message'] = 'Temporary folder can not be found';
                    //$res['mes'] = '没有找到临时目录';
                    break;
                case 7 :
                case 8 :
                    $res['code'] = 8;
                    $res['message'] = 'System error'; 
                    //$res['mes'] = '系统错误';
                    break;
            }
            return $res;
        }
    }

}

