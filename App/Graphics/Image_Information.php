<?php 

namespace App\Graphics;

use Common\Response as Response;

class Image_Information {

    protected function getPhotoAddr($hostname,$path,$filename) {
        return $hostname . substr($path,1) . '/' . $filename;
    }

    public function getOriginPhoto($connect, $originID, $hostName) {
        $hostName = 'http://' . $hostName;

        $goSql = "SELECT PATH, LOCAL_NAME FROM PHOTO WHERE VALID=1 AND PHOTO_ID='{$originID}'";

        $stGo = oci_parse($connect, $goSql);

        if (!oci_execute($stGo)) {
            Response::show(1401, 'Image_Information: query database by origin photo id error');
        }


        $photoAddr = '';
        if ($goRow = oci_fetch_array($stGo, OCI_BOTH)) {
            //var_dump($goRow);
            $localname = $goRow['LOCAL_NAME'];
            $path = $goRow['PATH'];
            $photoAddr = $this->getPhotoAddr($hostName,$path,$localname);
            $resData = array(
                'photoURL' => $photoAddr,
                );
            Response::show(1400,'Get origin photo successful', $resData);
        }

        // Photo do not exist
        Response::show(1402,'No such photo in database');

    }

}
