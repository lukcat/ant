<?php 

namespace App\Bus;

class BusInformation {
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
