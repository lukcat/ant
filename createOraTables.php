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
	$connect = Common\Oracle::getInstance()->connect();
} catch (Exception $e) {
	throw new Exception("Database connection error: " . mysql_error());
}



$table = 'feedback';
createTable($table, $connect);

//////////////////////////////////////////////////////////////
/////////////////////create table users//////////////////////
function createTable($tablename, $conn) {
	switch ($tablename) {
		case 'user':
		$field = '(userid varchar(50) not null primary key, name varchar(512),  email varchar(100), icard varchar(50), driverlicense varchar(50), phone varchar(50), cellphone varchar(50), address varchar(512), note text, valid int, password varchar(50), createtime timestamp, modifytime timestamp)';
		$sql = 'create table user' . $field;
		break;

		case 'photo':
		$field = '(photoid varchar(50) not null primary key, localname varchar(512), originname varchar(512), size varchar(50), type varchar(50), createtime timestamp, modifytime timestamp, description text, path varchar(512), valid int, feedbackid varchar(50))';
		$sql = 'create table photo' . $field;
		break;

		case 'feedback':
		$field = '(feedbackid varchar(50) not null primary key, content text, createtime timestamp, modifytime timestamp, valid int, userid varchar(50))';
		$sql = 'create table feedback' . $field;
		break;

		case 'thumbnail':
		$field = '(thumbnailid varchar(50) not null primary key, localname varchar(512), size varchar(50), type varchar(50), createtime timestamp, modifytime timestamp, description text, path varchar(512), valid int, photoid varchar(50))';
		$sql = 'create table thumbnail' . $field;
		break;

		default:
		echo 'no such table';
		break;
	}
	if (!$result = mysql_query($sql, $conn)) {
		//throw new Exception('Mysql query error: ' . mysql_error());
		//response message to client
		//数据库查询失败，返回错误信息，同时退出程序
		//Response::show(501,'Mobile_Register: query database by name error');
		return false;
	}
	return true;
}
