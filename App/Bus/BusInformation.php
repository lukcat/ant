<?php 

namespace App\Bus;

use Common\Response as Response;

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
            //return false;
            Response::show(1501,'BusInformation-getCountryVersion: query database error');
        }

        // get row
        if ($gvrow = oci_fetch_array($stgv, OCI_BOTH)) {
            $versionNumber = isset($gvrow['VERSION_NUM']) ? $gvrow['VERSION_NUM'] : '';

            $resData = array('version'=>$versionNumber);

            return $resData;
        }

        Response::show(1502, 'No city version data in database');
        //return false;
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
            //return false;
            Response::show(1701,'BusInformation-getCityInformation: query database error');
        }

        // Global varible
        $hasData = false;
        $cityList = array();

        // get city list
        while ($gcRows = oci_fetch_array($stgc, OCI_BOTH)) {
            $hasData    = true;
            $cityID     = isset($gcRows['CITY_ID']) ? $gcRows['CITY_ID'] : '';
            $cityName   = isset($gcRows['CITY_NAME']) ? $gcRows['CITY_NAME'] : '';
            $countryID  = isset($gcRows['COUNTRY_ID']) ? $gcRows['COUNTRY_ID'] : '';

            $city = array("cityID"=>$cityID, "cityName"=>$cityName);

            array_push($cityList, $city);
        }

        if (!$hasData) {
            Response::show(1702,'BusInformation-getCityInformation: No City basic information in database');
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
            Response::show(1801,'BusInformation-getBusLineInformation: query database error');
            //return false;
        }

        // global variable
        $hasData = false;
        $busInfo = array();
        $busLineInfo = array();
        $busList = array();

        // Global variable
        $busInfo['cityID'] = $cityID;

        // get busline list
        while ($gbRows = oci_fetch_array($stgb, OCI_BOTH)) {
            // set flag to be true, which means cityID is valid
            $hasData = true;

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

        if (!hasData) {
            Response::show(1802,'BusInformation-getBusLineInformation: No bus line information in database');
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
            Response::show(1803,'BusInformation-getBusstopInformation: query database error');
            //return false;
        }

        // global varibles
        $hasData = false;
        $stops = array();


        // Fetch result from bpRow
        while ($bpRows = oci_fetch_array($stbp)) {
            $hasData = true;

            $singleStop = array();
            $singleStop['stopName'] = $bpRows['BUSSTOP_NAME'];
            $singleStop['latitude'] = $bpRows['LATITUDE'];
            $singleStop['longitude'] = $bpRows['LONGITUDE'];

            array_push($stops, $singleStop);
        }

        if (!hasData) {
            Response::show(1804,'BusInformation-getBusstopInformation: No busstop information in database');
        }

        return $stops;
    }

}
