<?php
/*
 * Vehicle_Inquiry.php
 * Description: Inquiry vehicle information by license plate number, return json data to clients
 *  Created on: 2015/5/10
 *      Author: Chen Deqing
 */


namespace App\Inquiry;

use Common\Response as Response;

class Vehicle_Inquiry {
    // vehicleSeriesNumber, provence, district, vehicleType, company, range, license, owner, band, startyear

    /* check whether the vehicle is working
     * @param connect handler of oralce
     * @param vehicle_id 
     * return bool
     */
    function verifyVehicleID($connect, $vehicle_id) {
        $table = 'SECURITY_SUITE_WORKING ssw';
        $field = 'ssw.VEHICLE_ID';
        $condition = "ssw.VEHICLE_ID='{$vehicle_id}'";
        $sql = "SELECT {$field} FROM {$table} WHERE {$condition}";

        $ivid = oci_parse($connect, $sql);

        // commit 
        if(!oci_execute($ivid)) {
            //echo "commit failure";
            return false;
        }

        // get data from database
        if ($ivRows = oci_fetch_array($ivid, OCI_BOTH)) {
            $vehicleid = isset($ivRows['VEHICLE_ID']) ? $ivRows['VEHICLE_ID'] : '';

            return $vehicleid;
        }
        
        return false;
    }

	function getVehicleInfoByVehicleID($connect, $vehicle_id) {
        // tables
		$tables = 'VEHICLE v, VEHICLE_COMPANY c, DISTRICT d, SECURITY_SUITE_WORKING ssw, SECURITY_SUITE_INFO ssi';
		//$tables = 'VEHICLE v, VEHICLE_COMPANY c';

        // field
		$vehicleField = "v.VEHICLE_TYPE, v.BRAND_MODEL, v.START_YEAR, v.REGION, v.OPERATION_LICENSE, v.OWNER";
        $companyField = 'c.NAME AS COMPANY';
        $districtField = 'd.NAME AS DISTRICT';
        $sswField = 'ssw.VEHICLE_ID';
        $ssiField = 'ssi.ANT_SN, ssi.ANT_SN';
        $field = $vehicleField.','.$districtField.','.$companyField.','.$sswField.','.$ssiField; 

        // condition
	    $condition = "v.COMPANY_ID=c.ID AND v.DISTRICT_CODE=d.CODE AND v.VEHICLE_ID=ssw.VEHICLE_ID AND ssi.MDVR_CORE_SN=ssw.MDVR_CORE_SN AND ssw.STATUS IN (23,24) AND ssw.VEHICLE_ID='{$vehicle_id}'";
        //$condition = "ssi.MDVR_CORE_SN=ssw.MDVR_CORE_SN AND ssw.STATUS IN (23,24) AND ssi.ANT_SN='{$antid}'";
        $sql = "SELECT {$field} FROM {$tables} WHERE {$condition}";

        // parse
        $ivid = oci_parse($connect, $sql);

        // commit 
        if(!oci_execute($ivid)) {
            //echo "commit failure";
            return false;
        }

        // get data from database
        if ($ivRows = oci_fetch_array($ivid, OCI_BOTH)) {
            //var_dump($ivRows);
            $vehicleid = isset($ivRows['VEHICLE_ID']) ? $ivRows['VEHICLE_ID'] : '';
            //$vehicleType = isset($ivRows['VEHICLE_TYPE']) ? $ivRows['VEHICLE_TYPE'] : '';
            //$vehicleTypeNum = isset($ivRows['VEHICLE_TYPE']) ? $ivRows['VEHICLE_TYPE'] : '';
            $vehicleType = isset($ivRows['VEHICLE_TYPE']) ? $ivRows['VEHICLE_TYPE'] : '';
            $brandModel = isset($ivRows['BRAND_MODEL']) ? $ivRows['BRAND_MODEL'] : '';
            $startYear = isset($ivRows['START_YEAR']) ? $ivRows['START_YEAR'] : '';
            $operationLicense = isset($ivRows['OPERATION_LICENSE']) ? $ivRows['OPERATION_LICENSE'] : '';
            $owner = isset($ivRows['OWNER']) ? $ivRows['OWNER'] : '';
            $company = isset($ivRows['COMPANY']) ? $ivRows['COMPANY'] : '';
            $district = isset($ivRows['DISTRICT']) ? $ivRows['DISTRICT'] : '';
            $region = isset($ivRows['REGION']) ? $ivRows['REGION'] : '';
            $antSN= isset($ivRows['ANT_SN']) ? substr($ivRows['ANT_SN'],0,-2) : '';

            // Resolve vehicle type 
            /*
            switch ($vehicleTypeNum) {
                case '1':
                    $vehicleType = 'Taxi';
                    break;
                case '2':
                    $vehicleType = 'Bus';
                    break;
                case '3':
                    $vehicleType = 'Intercity Bus';
                    break;
                default:
                    $vehicleType = 'Unknown Type';
                    break;
            }
            */

            // struct data
            $vehicleInfo = array(
                    'vehicleid' => $vehicleid,
                    'vehicleType' => $vehicleType,
                    'brandModel' => $brandModel,
                    'startYear' => $startYear,
                    'operationLicense' => $operationLicense,
                    'owner' => $owner,
                    'company' => $company,
                    'district' => $district,
                    'region' => $region,
                    'antSN' => $antSN
                    );
            return $vehicleInfo;
        } else {
            return false;
        }
		
	}

    /*
	function getVehicleInfoByVehicleID($connect, $vehicle_id) {
        // tables
		$tables = 'VEHICLE v, VEHICLE_COMPANY c, DISTRICT d';
		//$tables = 'VEHICLE v, VEHICLE_COMPANY c';

        // field
		$vehicleField = "v.VEHICLE_ID, v.VEHICLE_TYPE, v.BRAND_MODEL, v.START_YEAR, v.REGION, v.OPERATION_LICENSE, v.OWNER";
        $companyField = 'c.NAME AS COMPANY';
        $districtField = 'd.NAME AS DISTRICT';
        $field = $vehicleField.','.$districtField.','.$companyField; 
        //$field = $vehicleField.','.$companyField; 

        // condition
		//$condition = "WHERE v.COMPANY_ID=c.COMPANY AND v.DISTRICT_CODE=d.CODE";
	    $condition = "v.COMPANY_ID=c.ID AND v.DISTRICT_CODE=d.CODE AND v.VEHICLE_ID='{$vehicle_id}'";
		//$sql = "select {$field} from {$table} {$condition}";
        $sql = "SELECT {$field} FROM {$tables} WHERE {$condition}";

        // parse
        $ivid = oci_parse($connect, $sql);

        // commit 
        if(!oci_execute($ivid)) {
            //echo "commit failure";
            return false;
        }

        // get data from database
        if ($ivRows = oci_fetch_array($ivid, OCI_BOTH)) {
            //var_dump($ivRows);
            $vehicleid = isset($ivRows['VEHICLE_ID']) ? $ivRows['VEHICLE_ID'] : '';
            //$vehicleType = isset($ivRows['VEHICLE_TYPE']) ? $ivRows['VEHICLE_TYPE'] : '';
            //$vehicleTypeNum = isset($ivRows['VEHICLE_TYPE']) ? $ivRows['VEHICLE_TYPE'] : '';
            $vehicleType = isset($ivRows['VEHICLE_TYPE']) ? $ivRows['VEHICLE_TYPE'] : '';
            $brandModel = isset($ivRows['BRAND_MODEL']) ? $ivRows['BRAND_MODEL'] : '';
            $startYear = isset($ivRows['START_YEAR']) ? $ivRows['START_YEAR'] : '';
            $operationLicense = isset($ivRows['OPERATION_LICENSE']) ? $ivRows['OPERATION_LICENSE'] : '';
            $owner = isset($ivRows['OWNER']) ? $ivRows['OWNER'] : '';
            $company = isset($ivRows['COMPANY']) ? $ivRows['COMPANY'] : '';
            $district = isset($ivRows['DISTRICT']) ? $ivRows['DISTRICT'] : '';
            $region = isset($ivRows['REGION']) ? $ivRows['REGION'] : '';

            // Resolve vehicle type 

            // struct data
            $vehicleInfo = array(
                    'vehicleid' => $vehicleid,
                    'vehicleType' => $vehicleType,
                    'brandModel' => $brandModel,
                    'startYear' => $startYear,
                    'operationLicense' => $operationLicense,
                    'owner' => $owner,
                    'company' => $company,
                    'district' => $district,
                    'region' => $region
                    );
            return $vehicleInfo;
        } else {
            return false;
        }
		
	}
    */

    // get vehicle id by antid
	function getVehicleIDByAntID($connect, $antid) {

        // tables
		$tables = 'SECURITY_SUITE_INFO ssi, SECURITY_SUITE_WORKING ssw';

        // field
		$field = 'ssw.VEHICLE_ID'; 
        // condition
        $condition = "ssi.MDVR_CORE_SN=ssw.MDVR_CORE_SN AND ssw.STATUS IN (23,24) AND ssi.ANT_SN='{$antid}'";

        // generate sql
        $sql = "SELECT {$field} FROM {$tables} WHERE {$condition}";
        //echo $sql; die();

        // parse
        $ivid = oci_parse($connect, $sql);

        // commit 
        if(!oci_execute($ivid)) {
            //echo "commit failure";
            return false;
        }

        // get data from database

        if ($ivRows = oci_fetch_array($ivid, OCI_BOTH)) {
            $vehicleid = isset($ivRows['VEHICLE_ID']) ? $ivRows['VEHICLE_ID'] : '';

            return $vehicleid;
        } else {
            return false;
        }

    }

    // get vehicle by antid
	function getVehicleInfoByAntID($connect, $antid) {
	    $vehicleid = $this->getVehicleIDByAntID($connect, $antid);

	    return $this->getVehicleInfoByVehicleID($connect, $vehicleid);
    }
}

/*
$vi = new Vehicle_Inquiry(); 
$connect = 'test';
$vehicle_id = 'GBH0600';
$vi->getVehicleInfo($connect, $vehicle_id); */ 
