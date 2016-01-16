<?php 

namespace App\Graphics;

use Common\Response as Response;

class Image_Processing {
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
    protected function getUniName(){
        return md5(uniqid(microtime(true),true));
    }

    public function generateThumbnail($connect, $files, $rootPath='./uploads/thumbnail', $columns=100, $rows=100, $bestfit=true) {
        $resData = array();
        $dateStr = date('Ymd');
        $savePath = $rootPath . '/' . $dateStr;

        foreach($files as $imageInfo) {
            //echo "tag";
            $imagePath = $imageInfo['path'] . '/' . $imageInfo['localname']; 
            //echo $imagePath;
            try{
                $rp = realpath($imagePath);
                $image = new \Imagick($rp);
            } catch (Exception $e) {
                throw new Exception("error occur in Image_Processing\generateThumbnail");
            }

            // generate thumbnail image
            $image->thumbnailImage($columns,$rows,$bestfit);

            // get image size
            $imageSize = $image->getImageSize();
            //$imageSize = $image->getsize();
            //$imageSize = getimagesize($destination);
            //var_dump($imageSize);

            // save image to local disk, get local path and local name
            // 1.Create folder
            if(!file_exists($savePath)){
                if(!mkdir($savePath,0777,true)){
                    $res['code'] = 1;
                    $res['message'] = 'Create folder failure';
                    //return $res;
                    //Response::show(701,"Create folder failure");
                }
                chmod($savePath,0777);
            }

            // 2.get unique name, extension and destination, then move file to the destination
            $ext=$this->getExt($imagePath);
            $uniName = $this->getUniName();
            $destination = $savePath.'/'.$uniName.'.'.$ext;

            if(!$image->writeImage($destination)) {
                $res['code'] = 2;
                $res['message'] = 'Can not write image to local system';
                //return $res;
                //Response::show(701,"Create folder failure");
            }

            // add origin name to array
            //$imageSize = $image->getImageSize();
            //echo 'imageSize';
            //echo $imageSize;die();
            $imageLocalName = $uniName.'.'.$ext;
            //$imageOriginName = basename($imagePath);
            $imageOriginName = $imageInfo['originname'];
            $imageType = $ext;
            $imagePath = $savePath;     // here is relative path
            $photoID = $imageInfo['photoid'];

            /*
            $insertData = Array(
                    'imageLocalName' => $imageLocalName,
                    'imageOriginName' => $imageOriginName,
                    'imageSize' => $imageSize,
                    'imageType' => $imageType,
                    'imagePath' => $imagePath
                    );
            */

            if (!isset($res['code'])) {
                $res['code'] = 0;
                $res['message'] = 'Thumbnail ' . $imageOriginName . ' generated successful';
            }

            list($width, $height) = getimagesize($destination);
            $res['path'] = $imagePath;
            $res['localname'] = $imageLocalName;
            $res['type'] = $imageType;
            $res['originname'] = $imageOriginName;
            $res['size'] = $imageSize;
            $res['width'] = $width;
            $res['height'] = $height;
            $res['description'] = 'No description yet';
            $res['photoid'] = $photoID;

            //$res['data'] = $fileInfo;
            array_push($resData,$res);
            //return $res;
            // return data
        }
        return $resData;
    }


    public function insertThumbnailInfo($connect, $thumbnailInfo, $complaint_id) {


        $thumbnailid = md5(uniqid(microtime(true),true));   // primary key
        $photoid = $thumbnailInfo['photoid'];               // foreign key
        //$complaintid = $thumbnailInfo['complaintid'];       // foreign key
        $complaintid = $complaint_id;       // foreign key

        $localname = $thumbnailInfo['localname'];           // file name in local system
        $originname = $thumbnailInfo['originname'];         // file origin name
        $size = $thumbnailInfo['size'];                     // file size
        $width = $thumbnailInfo['width'];
        $height = $thumbnailInfo['height'];
        $type = $thumbnailInfo['type'];                     // file type
        $valid = 1;                                     // 1 represent effective, 0 reprensent ineffective
        $path = $thumbnailInfo['path'];                     // file's relative path
        $description = $thumbnailInfo['description'];       // file description
        $createtime = date('Y-m-d H:i:s');              // file's create_time
        $modifytime = date('Y-m-d H:i:s');              // file's modify_time 

        // sql
        $sql="INSERT INTO THUMBNAIL(THUMBNAIL_ID,PHOTO_ID,COMPLAINT_ID,LOCAL_NAME,ORIGIN_NAME,PHOTO_SIZE,PHOTO_WIDTH, PHOTO_HEIGHT, TYPE,VALID,PATH,DESCRIPTION,CREATE_TIME,MODIFY_TIME) VALUES ('{$thumbnailid}','{$photoid}','{$complaintid}','{$localname}','{$originname}',{$size},{$width},{$height},'{$type}',{$valid},'{$path}','{$description}',to_date('{$createtime}','yyyy-mm-dd hh24:mi:ss'),to_date('{$modifytime}','yyyy-mm-dd hh24:mi:ss'))";

        //echo $sql;die();
        $thumbnailInfo['thumbnailid'] = $thumbnailid;

        $stid = oci_parse($connect,$sql);

        if(!oci_execute($stid)) {
            return false;
        } else {
            return $thumbnailInfo;
        }
        
    }
}
