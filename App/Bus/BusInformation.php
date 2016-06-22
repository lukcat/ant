<?php 

namespace App\Bus;

class BusInformation {
    public function getBusLineInformation($connect, $cityID) {
    //public function getBusInformation($connect, $cityName) {
        //$cityID = $this->getCityID($connect, $cityName); 
        //echo "cityID";
        //echo $cityID;
        if (!$cityID) {
            return false;
        }

        $busLineInfo = $this->getBusLine($connect, $cityID);
        $busstopInfo = $this->getBusstop($connect, $cityID);
        //$versionInfo = $this->getVersion($connect, $cityID);

        if (empty($busLineInfo)) {
            $busLineInfo = '';
        }
        if (empty($busstopInfo)) {
            $busstopInfo = '';
        }
        //if (empty($versionInfo)) {
        //    $versionInfo = '';
        //}

        // formate data as JSON
        $resData = array();
        //$resData['cityName'] = $cityName;
        //$resData['version'] = $versionInfo;

        $resData['busline'] = $busLineInfo;
        $resData['busstop'] = $busstopInfo;

        return $resData;
    }

    private function getCityID($connect, $cityName) {
        // get cityID
        $cityIDSql  = "SELECT CITY_ID FROM BUS_CITY WHERE CITY_NAME='{$cityName}'";
        // parse
        $stci = oci_parse($connect, $cityIDSql);
        // execute
        if (!oci_execute($stci)) {
            return false;
        }
        // get data
        if ($ciRows = oci_fetch_array($stci, OCI_BOTH)) {
            $cityID = preg_replace("/\s/","",$ciRows['CITY_ID']);
            //$cityID = $ciRows['CITY_ID'];
            return $cityID;
        } else {
            return false;
        }
    }

    private function getBusLine($connect, $cityID) {
        //$buslineSql = "SELECT * FROM BUS_LINE WHERE CITY_ID='{$cityID}'";
        $buslineSql = "SELECT BL_ID, BL_NAME, DIRECTION, STOPS, POINTS_NUMS, POINTS FROM MAPP_BUS_LINE WHERE CITY_ID='{$cityID}'";
        // parse
        $stbl = oci_parse($connect, $buslineSql);
        // execute
        if (!oci_execute($stbl)) {
            return false;
        }
        // get data
        $busline = array();
        while ($blRows = oci_fetch_array($stbl, OCI_ASSOC)) {
            //$cityID = preg_replace("/\s/","",$ciRows['CITY_ID']);
            //$lineInfo['buslineid'] = $blRows['BL_ID'];
            $lineInfo = array();
            $lineInfo['bl_id'] = preg_replace("/\s/","",$blRows['BL_ID']);
            $lineInfo['city_id'] = $cityID;
            $lineInfo['bl_name'] = $blRows['BL_NAME'];
            $lineInfo['direction'] = $blRows['DIRECTION'];
            $lineInfo['stops'] = $blRows['STOPS'];
            //$lineInfo['point_nums'] = $blRows['POINTS_NUMS'];
            // read method not being limited by the script memory limit
            $lineInfo['points'] = $blRows['POINTS']->read(2000);
            // or load method
            //$lineInfo['points'] = $blRows['POINTS']->load();
            // but the one below is wrong
            //$lineInfo['points'] = $blRows['POINTS'];

            array_push($busline, $lineInfo);
        }

        return $busline;
    }

    private function getBusstop($connect, $cityID) {
        //$busstopSql = "SELECT * FROM BUSSTOP WHERE CITY_ID='{$cityID}'";
        $busstopSql = "SELECT BS_ID, BUSSTOP_NAME, LATITUDE, LONGITUDE FROM MAPP_BUSSTOP WHERE CITY_ID='{$cityID}'";
        // parse
        $stbs = oci_parse($connect, $busstopSql);
        // execute
        if (!oci_execute($stbs)) {
            return false;
        }
        // get data
        $busstop = array();
        while ($bsRows = oci_fetch_array($stbs, OCI_ASSOC)) {
            //$cityID = preg_replace("/\s/","",$ciRows['CITY_ID']);
            $busstopInfo = array();
            $busstopInfo['bs_id'] = preg_replace("/\s/","",$bsRows['BS_ID']);
            $busstopInfo['city_id'] = $cityID;
            $busstopInfo['busstop_name'] = $bsRows['BUSSTOP_NAME'];
            $busstopInfo['longitude'] = $bsRows['LONGITUDE'];
            $busstopInfo['latitude'] = $bsRows['LATITUDE'];
            
            array_push($busstop, $busstopInfo);
        }

        return $busstop;
    }

    private function getVersion($connect, $cityID) {
        $versionSql = "SELECT VERSION_NUM FROM VERSION WHERE CITY_ID='{$cityID}'";
        // parse
        $stvs = oci_parse($connect, $versionSql);
        // execute
        if (!oci_execute($stvs)) {
            return false;
        }
        // get data
        if ($vsRows = oci_fetch_array($stvs, OCI_ASSOC)) {
            //$cityID = preg_replace("/\s/","",$ciRows['CITY_ID']);
            return $vsRows['VERSION_NUM'];
        } else {
            return false;
        }
    }

    /* Get country version number
     * @param: countryID
     * @param: connect
     * @return: country_version_number
     */
    public function getCountryVersion($connect, $countryID) {
        // sql sentence, query COUNTRY_VERSION table
        $get_version = "SELECT VERSION_NUM FROM MAPP_COUNTRY_VERSION WHERE VERSION_ID='{$countryID}'";

        // parse
        $stgv = oci_parse($connect, $get_version);

        // execute
        if (!oci_execute($stgv)) {
            //return false;
            Response::show(1501,'BusInformation-getCountryVersion: query database error');
        }

        // get row
        if ($gvrow = oci_fetch_array($stgv, OCI_BOTH)) {
            $versionNumber = isset($gvrow['VERSION_NUM']) ? $gvrow['VERSION_NUM'] : '';

            $resData = array('version'=>$versionNumber);

            return $resData;
        }

        Response::show(1502, 'No country version data in database');
        //return false;
    }

    /* Get city version number
     * @param: cityID
     * @param: connect
     * @return: city_version_number
     */
    public function getCityVersion($connect, $cityID) {
        $get_version = "SELECT VERSION_NUM FROM MAPP_CITY_VERSION WHERE VERSION_ID='{$cityID}'";

        // parse
        $stgv = oci_parse($connect, $get_version);

        // execute
        if (!oci_execute($stgv)) {
            //return false;
            Response::show(1601,'BusInformation-getCityVersion: query database by cityID error');
        }

        // flag 
        // get row
        if ($gvrow = oci_fetch_array($stgv, OCI_BOTH)) {
            $versionNumber = isset($gvrow['VERSION_NUM']) ? $gvrow['VERSION_NUM'] : '';

            $resData = array('version'=>$versionNumber);

            return $resData;
        }

        Response::show(1602, 'No city version data in database');
        //return false;
    }

    /* Get city information
     * @param: cityID 
     * @param: connect
     * @return: array(json formation data which contains all of citys of specific country)
     */
    public function getCityInformation($connect) {
        // sql sentence 
        $get_city = "SELECT CITY_ID, COUNTRY_ID, CITY_NAME FROM MAPP_CITY";

        // parse
        $stgc = oci_parse($connect, $get_city);

        // execute sql
        if (!oci_execute($stgc)) {
            //return false;
            Response::show(1701,'BusInformation-getCityInformation: query database error');
        }

        // Global varible
        $hasData = false;
        $cityList = array();

        // get city list
        while ($gcRows = oci_fetch_array($stgc, OCI_BOTH)) {
            $hasData    = true;
            $cityID     = isset($gcRows['CITY_ID']) ? preg_replace("/\s/","",$gcRows['CITY_ID']) : '';
            $cityName   = isset($gcRows['CITY_NAME']) ? $gcRows['CITY_NAME'] : '';
            $countryID  = isset($gcRows['COUNTRY_ID']) ? preg_replace("/\s/","",$gcRows['COUNTRY_ID']) : '';

            $city = array("cityID"=>$cityID, "cityName"=>$cityName);

            array_push($cityList, $city);
        }

        if (!$hasData) {
            Response::show(1702,'BusInformation-getCityInformation: No City basic information in database');
        }

        /* Get version number */
        $countryVersion = $this->getCountryVersion($connect,$countryID);

        //var_dump($cityInfo);die();
        $cityInfo['countryVersion'] = $countryVersion['version'];
        $cityInfo['countryID'] = $countryID;
        $cityInfo['cityInfo'] = $cityList;

        return $cityInfo;
    }
}
