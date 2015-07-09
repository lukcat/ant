<?php 

namespace App\Graphics

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

    public function generateThumbnail($connect, $imagePath, $savePath='./uploads/thumbnail', $columns=50, $rows=50, $bestfit=true) {
        try{
            $image = new Imagick(realpath($imagePath));
        } catch (Exception $e) {
            throw new Exception("error occur in Image_Processing\generateThumbnail");
        }

        // generate thumbnail image
        $image->thumbnailImage($columns,$rows,$bestfit);
        
        // get image size
        $imageSize = $image->getImageSize();

        // save image to local disk, get local path and local name
        // 1.Create folder
        if(!file_exists($savePath)){
            if(!mkdir($savePath,0777,true)){
                $res['code'] = 1;
                $res['message'] = 'Create folder failure';
                return $res;
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
            return $res;
        }

        // add origin name to array
        $imageSize = $image->getImageSize();
        $imageLocalName = $uniName;
        $imageOriginName = basename($imagePath);
        $imageType = $ext;
        $imagePath = $savePath;     // here is relative path

        $insertData = Array(
                'imageLocalName' => $imageLocalName,
                'imageOriginName' => $imageOriginName,
                'imageSize' => $imageSize,
                'imageType' => $imageType,
                'imagePath' => $imagePath
                );

        $res['code'] = 0;
        $res['message'] = 'Insert thoes data into database';
        $res['data'] = $insertData;

        return $res;
        // return data
    }
}
