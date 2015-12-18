<?php 

namespace App\Bus;

class BusInformation {

    /* Get city version number
     * @param: cityID
     * @param: connect
     * @return: city_version_number
     */
    public function getCityVersion($connect, $cityID) {
        $get_version = "SELECT VERSION_NUM FROM CITY_VERSION WHERE VERSION_ID='{$cityID}'";

        // parse
        $stgv = oci_parse($connect, $get_version);

        // execute
        if (!oci_execute($stgv)) {
            return false;
        }

        // get row
        if ($gvrow = oci_fetch_array($stgv, OCI_BOTH)) {
            $versionNumber = isset($gvrow['VERSION_NUM']) ? $gvrow['VERSION_NUM'] : '';

            $resData = array('version'=>$versionNumber);

            return $resData;
        }

        return false;
    }

    /* Get country version number
     * @param: countryID
     * @param: connect
     * @return: country_version_number
     */
    public function getCountryVersion($connect, $countryID) {
        // sql sentence, query COUNTRY_VERSION table
        $get_version = "SELECT VERSION_NUM FROM COUNTRY_VERSION WHERE VERSION_ID='{$countryID}'";

        // parse
        $stgv = oci_parse($connect, $get_version);

        // execute
        if (!oci_execute($stgv)) {
            return false;
        }

        // get row
        if ($gvrow = oci_fetch_array($stgv, OCI_BOTH)) {
            $versionNumber = isset($gvrow['VERSION_NUM']) ? $gvrow['VERSION_NUM'] : '';

            $resData = array('versionNumber'=>$versionNumber);

            return $resData;
        }

        return false;
    }

    /* Get city information
     * @param: cityID 
     * @param: connect
     * @return: array(json formation data which contains all of citys of specific country)
     */
    public function getCityInformation($connect) {
        // sql sentence 
        $get_city = "SELECT CITY_ID, COUNTRY_ID, CITY_NAME FROM CITY";

        // parse
        $stgc = oci_parse($connect, $get_city);

        // execute sql
        if (!oci_execute($stgc)) {
            return false;
        }

        $cityList = array();

        // get city list
        while ($gcRows = oci_fetch_array($stgc, OCI_BOTH)) {
            $cityID     = isset($gcRows['CITY_ID']) ? $gcRows['CITY_ID'] : '';
            $cityName   = isset($gcRows['CITY_NAME']) ? $gcRows['CITY_NAME'] : '';
            $countryID  = isset($gcRows['COUNTRY_ID']) ? $gcRows['COUNTRY_ID'] : '';

            $city = array("cityID"=>$cityID, "cityName"=>$cityName);

            array_push($cityList, $city);
        }

        /* Get version number */
        $countryVersion = $this->getCountryVersion($connect,$countryID);

        //var_dump($cityInfo);die();
        $cityInfo['versionNumber'] = $countryVersion['versionNumber'];
        $cityInfo['countryID'] = $countryID;
        $cityInfo['cityInfo'] = $cityList;

        return $cityInfo;
    }

    /* Get bus line information
     * @param: cityID
     * @param: connect
     * @return: array(json formation data which contains all of bus line information of specific city)
     */
    public function getBusLineInformation($connect, $cityID) {
        /* query BUS_LINE table, get basic information of busline
         */
        // query sentence
        $getBusLine = "SELECT BL_NAME, DIRECTION, STOPS, POINTS_NUMS, POINTS FROM BUS_LINE WHERE CITY_ID='{$cityID}'";
        //echo $getBusLine;die();

        // parse
        $stgb = oci_parse($connect, $getBusLine);

        // execute sql
        if (!oci_execute($stgb)) {
            return false;
        }

        // global variable
        $flag   = false;
        $busInfo = array();
        $busLineInfo = array();
        $busList = array();

        $busInfo['cityID'] = $cityID;

        // get busline list
        while ($gbRows = oci_fetch_array($stgb, OCI_BOTH)) {
            // set flag to be true, which means cityID is valid
            $flag = true;

            //var_dump($gbRows);die();

            //$cityID     = isset($gbRows['CITY_ID']) ? $gbRows['CITY_ID'] : '';
            $lineNum    = isset($gbRows['BL_NAME']) ? $gbRows['BL_NAME'] : '';
            $direction  = isset($gbRows['DIRECTION']) ? $gbRows['DIRECTION'] : '';
            $stopsID      = isset($gbRows['STOPS']) ? $gbRows['STOPS'] : '';
            $pointNum   = isset($gbRows['POINTS_NUMS']) ? $gbRows['POINTS_NUMS'] : '';
            $points     = isset($gbRows['POINTS']) ? $gbRows['POINTS']->load() : 'xxx';

            $busLine = array();
            $busLine['line'] = $lineNum;
            $busLine['direction'] = $direction;
            $busLine['point_nums'] = $pointNum;
            $busLine['points'] = $points;
            //$busLine['stops'] = $stopsID;

            /* query BUSSTOP table, get basic information of busstop
             */
            $stops = $this->getBusstopInformation($connect, $stopsID);

            $busLine['stops'] = $stops;

            array_push($busLineInfo, $busLine);
        }

        $version = $this->getCityVersion($connect, $cityID); 
        
        $busInfo['version'] = $version['versionNumber'];
        $busInfo['busLineInfo'] = $busLineInfo;

        return $busInfo;
    }

    /* Get bus stop positon
     * @param: stopIDs //bus stop id which is related to busstop table
     * @param: connect
     * @return: array(json formation data which contains all of bus stop information of specific city)
     */
    public function getBusstopInformation($connect, $stopsID) {
        // Get all of the bus stops postion
        $getStopsPosition = "SELECT BUSSTOP_NAME, LATITUDE, LONGITUDE FROM BUSSTOP WHERE BS_ID IN ({$stopsID})";
        //echo $getStopsPosition;die();

        // parse the sql above
        $stbp = oci_parse($connect, $getStopsPosition);

        // execute sql
        if (!oci_execute($stbp)) {
            return false;
        }

        // global varibles
        $stops = array();


        // Fetch result from bpRow
        while ($bpRows = oci_fetch_array($stbp)) {
            $singleStop = array();
            $singleStop['stopName'] = $bpRows['BUSSTOP_NAME'];
            $singleStop['latitude'] = $bpRows['LATITUDE'];
            $singleStop['longitude'] = $bpRows['LONGITUDE'];

            array_push($stops, $singleStop);
        }

        return $stops;
    }


    public function getBusInformation($connect, $cityName) {
        $cityID = $this->getCityID($connect, $cityName); 
        //echo "cityID";
        //echo $cityID;
        if (!$cityID) {
            return false;
        }

        $busLineInfo = $this->getBusLine($connect, $cityID);
        $busstopInfo = $this->getBusstop($connect, $cityID);
        $versionInfo = $this->getVersion($connect, $cityID);

        if (empty($busLineInfo)) {
            $busLineInfo = '';
        }
        if (empty($busstopInfo)) {
            $busstopInfo = '';
        }
        if (empty($versionInfo)) {
            $versionInfo = '';
        }

        // formate data as JSON
        $resData = array();
        $resData['cityName'] = $cityName;
        $resData['version'] = $versionInfo;

        $resData['busLine'] = $busLineInfo;
        $resData['busstopInfo'] = $busstopInfo;

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
        $buslineSql = "SELECT * FROM BUS_LINE WHERE CITY_ID='{$cityID}'";
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
            $lineInfo['buslineid'] = preg_replace("/\s/","",$blRows['BL_ID']);
            $lineInfo['line'] = $blRows['BL_NAME'];
            $lineInfo['direction'] = $blRows['DIRECTION'];
            $lineInfo['stops'] = $blRows['STOPS'];
            $lineInfo['point_nums'] = $blRows['POINTS_NUMS'];
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
        $busstopSql = "SELECT * FROM BUSSTOP WHERE CITY_ID='{$cityID}'";
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
            $busstopInfo['busID'] = preg_replace("/\s/","",$bsRows['BS_ID']);
            $busstopInfo['stopName'] = $bsRows['BUSSTOP_NAME'];
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

}
