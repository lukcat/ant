<?php

namespace App\VehicleHisToryRoute;

use Common\Response as Response;

class VehicleBasicInformation {
    public function getCompanyList($connect) {
        // district: code name shortname fullname valid note
        // company: id name company_type district_code phone fax email address contact contact_phone contact_email note valid

        // sql sentence 
        $get_district = "SELECT CODE, NAME, SHORTNAME, FULLNAME, NOTE FROM MAPP_DISTRICT";
        $get_company = "SELECT ID, NAME, COMPANY_TYPE, DISTRICT_CODE, PHONE, FAX, EMAIL, ADDRESS, CONTACT, CONTACT_PHONE, CONTACT_EMAIL, NOTE FROM MAPP_COMPANY";
// parse $stgd = oci_parse($connect, $get_district); $stgc = oci_parse($connect, $get_company); 

        // parse sql
        $stgd = oci_parse($connect, $get_district);
        $stgc = oci_parse($connect, $get_company);

        // execute sql
        if (!oci_execute($stgd)) {
            //return false;
            Response::show(2201,'VehicleHistoryRoute: query database(district) error');
        }
        if (!oci_execute($stgc)) {
            //return false;
            Response::show(2201,'VehicleHistoryRoute: query database(company) error');
        }

        $districtList = array();
        $companyList = array();

        // get district list
        while ($gdRows = oci_fetch_array($stgd, OCI_BOTH)) {
            //$get_district = "SELECT CODE, NAME, SHORTNAME, FULLNAME, NOTE FROM MAPP_DISTRICT";
            //$code       = isset($gvRows['CODE']) ? preg_replace("/\s/","",$gcRows['CODE']) : '';
            $code       = isset($gdRows['CODE']) ? $gdRows['CODE'] : '';
            $name       = isset($gdRows['NAME']) ? $gdRows['NAME'] : '';
            $shortName  = isset($gdRows['SHORTNAME']) ? $gdRows['SHORTNAME'] : '';
            $fullName   = isset($gdRows['FULLNAME']) ? $gdRows['FULLNAME'] : '';
            $note       = isset($gdRows['NOTE']) ? $gdRows['NOTE'] : '';

            $district = array("code"=>$code, "name"=>$name, "shortName"=>$shortName, "fullName"=>$fullName);

            array_push($districtList, $district);
        }

        // get company list
        while ($gvRows = oci_fetch_array($stgc, OCI_BOTH)) {
            //$id         = isset($gvRows['CITY_ID']) ? preg_replace("/\s/","",$gcRows['CITY_ID']) : '';
            $id             = isset($gvRows['ID']) ? $gvRows['ID'] : '';
            $name           = isset($gvRows['NAME']) ? $gvRows['NAME'] : '';
            $companyType    = isset($gvRows['COMPANY_TYPE']) ? $gvRows['COMPANY_TYPE'] : '';
            $districtCode   = isset($gvRows['DISTRICT_CODE']) ? $gvRows['DISTRICT_CODE'] : '';
            $phone          = isset($gvRows['PHONE']) ? $gvRows['PHONE'] : '';
            $fax            = isset($gvRows['FAX']) ? $gvRows['FAX'] : '';
            $email          = isset($gvRows['EMAIL']) ? $gvRows['EMAIL'] : '';
            $address        = isset($gvRows['ADDRESS']) ? $gvRows['ADDRESS'] : '';
            $contact        = isset($gvRows['CONTACT']) ? $gvRows['CONTACT'] : '';
            $contactPhone   = isset($gvRows['CONTACT_PHONE']) ? $gvRows['CONTACT_PHONE'] : '';
            $contactEmail   = isset($gvRows['CONTACT_EMAIL']) ? $gvRows['CONTACT_EMAIL'] : '';
            $note           = isset($gvRows['NOTE']) ? $gvRows['NOTE'] : '';

            $company = array("id"=>$id, "name"=>$name, "companyType"=>$companyType, "districtCode"=>$districtCode, "phone"=>$phone, "fax"=>$fax, "email"=>$email, "address"=>$address, "contact"=>$contact, "contactPhone"=>$contactPhone, "contactEmail"=>$contactEmail, "note"=>$note);

            array_push($companyList, $company);
        }

        // response data
        $resData = array();
        $resData["district"] = $districtList;
        $resData["company"] = $companyList;

        return $resData;
    }

    public function getVehicleList($connect, $companyId) {
    //public function getVehicleList($connect) {
        // sql sentance
        $get_vehicle = "SELECT VEHICLE_ID,VEHICLE_TYPE, VEHICLE_STATUS, BRAND_MODEL, START_YEAR, SERVICE_TYPE, COMPANY_ID, REGION, OPERATION_LICENSE, DISTRICT_CODE, OWNER, OWNER_ID, OWNER_PHONE, OWNER_EMAIL, OWNER_ADDRESS, NOTE  FROM MAPP_VEHICLE WHERE COMPANY_ID = '{$companyId}'";
        //$get_vehicle = "SELECT VEHICLE_TYPE, VEHICLE_STATUS, BRAND_MODEL, START_YEAR, SERVICE_TYPE, COMPANY_ID, REGION, OPERATION_LICENSE, DISTRICT_CODE, OWNER, OWNER_ID, OWNER_PHONE, OWNER_EMAIL, OWNER_ADDRESS, NOTE  FROM MAPP_VEHICLE";
            
        //var_dump($get_vehicle);die();
        // parse sql
        $stgv = oci_parse($connect, $get_vehicle);

        // execute sql
        if (!oci_execute($stgv)) {
            //return false;
            Response::show(2201,'VehicleHistoryRoute: query database error');
        }

        $vehicleList = array();

        // get district list
        while ($gvRows = oci_fetch_array($stgv, OCI_BOTH)) {
            //$get_district = "SELECT CODE, NAME, SHORTNAME, FULLNAME, NOTE FROM MAPP_DISTRICT";
            //$code       = isset($gvRows['CODE']) ? preg_replace("/\s/","",$gcRows['CODE']) : '';
            //$get_vehicle = "SELECT VEHICLE_TYPE, VEHICLE_STATS, BRAND_MODEL, START_YEAR, SERVICE_TYPE, COMPANY_ID, REGION, OPERATION_LICENSE, DISTRICT_CODE, OWNER, OWNER_ID, OWNER_PHONE, OWNER_EMAIL, OWNER_ADDRESS, NOTE  FROM MAPP_VEHICLE";
            $vehicleId    = isset($gvRows['VEHICLE_ID']) ? $gvRows['VEHICLE_ID'] : '';
            $vehicleType    = isset($gvRows['VEHICLE_TYPE']) ? $gvRows['VEHICLE_TYPE'] : '';
            $vehicleStatus  = isset($gvRows['VEHICLE_STATUS']) ? $gvRows['VEHICLE_STATUS'] : '';
            $brandModel     = isset($gvRows['BRAND_MODEL']) ? $gvRows['BRAND_MODEL'] : '';
            $startYear      = isset($gvRows['START_YEAR']) ? $gvRows['START_YEAR'] : '';
            $serviceType    = isset($gvRows['SERVICE_TYPE']) ? $gvRows['SERVICE_TYPE'] : '';
            $companyId      = isset($gvRows['COMPANY_ID']) ? $gvRows['COMPANY_ID'] : '';
            $region         = isset($gvRows['REGION']) ? $gvRows['REGION'] : '';
            $operationLicense= isset($gvRows['OPERATION_LICENSE']) ? $gvRows['OPERATION_LICENSE'] : '';
            $districtCode       = isset($gvRows['DISTRICT_CODE']) ? $gvRows['DISTRICT_CODE'] : '';
            $owner              = isset($gvRows['OWNER']) ? $gvRows['OWNER'] : '';
            $ownerId            = isset($gvRows['OWNER_ID']) ? $gvRows['OWNER_ID'] : '';
            $ownerPhone         = isset($gvRows['OWNER_PHONE']) ? $gvRows['OWNER_PHONE'] : '';
            $ownerEmail         = isset($gvRows['OWNER_EMAIL']) ? $gvRows['OWNER_EMAIL'] : '';
            $ownerAddress       = isset($gvRows['OWNER_ADDRESS']) ? $gvRows['OWNER_ADDRESS'] : '';
            $note               = isset($gvRows['NOTE']) ? $gvRows['NOTE'] : '';

            $vehicle = array('vehicleId'=>$vehicleId,'vehicleType'=>$vehicleType, 'vehicleStatus'=>$vehicleStatus, 'brandModel'=>$brandModel, 'startYear'=>$startYear, 'companyId'=>$companyId, 'region'=>$region, 'operationLicense'=>$operationLicense, 'districtCode'=>$districtCode, 'owner'=>$owner, 'ownerId'=>$ownerId, 'ownerPhone'=>$ownerPhone, 'owner_email'=>$ownerEmail, 'ownerAddress'=>$ownerAddress, 'note'=>$note);

            array_push($vehicleList, $vehicle);
        }

        // response data
        $resData = array();
        $resData["vehicle"] = $vehicleList;

        return $resData;
    }
}
