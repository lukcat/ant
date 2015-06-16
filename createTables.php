<?php
/*
 * CreateTables.php
 * Description: This is program main entrance, according to user action and calling relevant module
 *  Created on: 2015/4/29
 *      Author: Chen Deqing
 */


define('BASEDIR',__DIR__);

include BASEDIR . '/Common/Loader.php';

// use PSR-0 coding standard
spl_autoload_register('\\Common\\Loader::autoload');


// generate database handle
//$connect = Common\Db::getInstance()->connect();
try {
	$connect = Common\Db::getInstance()->connect();
} catch (Exception $e) {
	throw new Exception("Database connection error: " . mysql_error());
}

// create file
if (createFileTable($connect)) {
	echo "file table is created in database.\n";
} else {
	echo "can not connect database or file table is already exist.\n";
}

// create vehicle
if (createVehicleTable($connect)) {
	echo "vehicle table is created in database.\n";
} else {
	echo "can not connect database or vehicle table is already exist.\n";
}

// insert into vehicle
insertVehicleTable($connect);

//////////////////////////////////////////////////////////////
/////////////////////create table file //////////////////////
function createFileTable($conn) {
	$field = 'imageid char(50) not null primary key, localname varchar(512), originname varchar(512),type varchar(50), size int, path varchar(512), time timestamp not null default now(), description text, userid char(50)';
	$sql = 'create table file (' . $field . ')';
	if (!$result = mysql_query($sql, $conn)) {
		//throw new Exception('Mysql query error: ' . mysql_error());
		// response message to client
		// 数据库查询失败，返回错误信息，同时退出程序
		//Response::show(501,'Mobile_Register: query database by name error');
		return false;
	}
	return true;
}

//////////////////////////////////////////////////////////////
/////////////////////create table vehicle//////////////////////
function createVehicleTable($conn) {
	$field = 'vehicle_id varchar(32) not null primary key, vehicle_sn varchar(50), engine_id varchar(50), vehicle_type int, vehicle_status int, brand_model varchar(100), start_year varchar(10), service_type int, company_id varchar(36), region varchar(512), operation_license varchar(100), district_code varchar(15), valid int, owner varchar(512), owner_id varchar(50), owner_phone varchar(50), owner_email varchar(100), owner_address varchar(512), note varchar(2000)';
	$sql = 'create table vehicle (' . $field . ')';
	if (!$result = mysql_query($sql, $conn)) {
		//throw new Exception('Mysql query error: ' . mysql_error());
		return false;
	}
	return true;
}

//////insert test data to table vehicle////
function insertVehicleTable($conn) {
	// testvalue
	//$vehicle_id = 'vehicleID123456789';
	$vehicle_id = md5(uniqid(microtime(true),true));
	$vehicle_sn = 'vehicleSN123456789';
	$engine_id = 'engineID123456789';

	// show 
	$vehicle_type = 0; // 0是出租车，１是公交车，２是长途大巴

	$vehicle_status = 1;	// 0是不具备，１是具备．缺省是１
	$brand_model = 'brandModel';
	$service_type = 44;

	// show
	$company_id = 'antCompany';

	$region = 'someplace';
	$operation_lecense = 'operation_lecense';
	$district_code = '100010';
	$valid = 55;
	$owner = 'TuHao';

	// show 
	$owner_id = 'tuhaoniubi';
	$owner_phone = '13225025025';
	$owner_email = 'tuhao@tuhao.com';
	$owner_address = 'niubiAddress';

	$note = 'this car is very tuhao';

	$field = 'vehicle_id, vehicle_sn, engine_id, vehicle_type, vehicle_status, brand_model, start_year, service_type, company_id, region, operation_license, district_code, valid, owner, owner_id, owner_phone, owner_email, owner_address, note';

	$value = "'{$vehicle_id}', '{$vehicle_sn}', '{$engine_id}', '{$vehicle_type}', '{$vehicle_status}', '{$brand_model}', '{$start_year}', '{$service_type}', '{$company_id}', '{$region}', '{$operation_license}', '{$district_code}', '{$valid}', '{$owner}', '{$owner_id}', '{$owner_phone}', '{$owner_email}', '{$owner_address}', '{$note}'";

	//echo $field;
	//echo $value;

	$sql = 'insert into vehicle (' . $field. ') values (' . $value . ')';

	echo $sql;
	//exit;

	if (!$result = mysql_query($sql, $conn)) {
		throw new Exception('Mysql query error: ' . mysql_error());
		return false;
	}
	return true;
}
		


