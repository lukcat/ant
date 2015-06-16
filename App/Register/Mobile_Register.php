<?php
/*
 * Mobile_Registr.php
 * Description: This is program main entrance, according to user action and calling relevant module
 *  Created on: 2015/4/10
 *      Author: Chen Deqing
 */

namespace App\Register;

// 使用别名: use Common\Response 相当于 use Common\Response as Response
use Common\Response as Response;

class Mobile_Register {
	function nameExist($username, $connect) {	// 检查用户名是否存在, 存在返回true, 不存在返回false
		
		$check_sql = 'select * from login where name = ' . '"' . $username . '"';

		if (!$result = mysql_query($check_sql, $connect)) {
				throw new Exception('Mysql query error: ' . mysql_error());
				// response message to client
				// 数据库查询失败，返回错误信息，同时退出程序
				Response::show(501,'Mobile_Register: query database by name error');
		}
		if ($rows = mysql_fetch_array($result, MYSQL_ASSOC)) {
				// user already exist
				return true;
		}

		// user do not exist;
		return false;
	}
	
	function register($username, $password, $connect) {
		
		if ($username != '' && $password != '') {
			if (!self::nameExist($username, $connect)) {	// 名字不存在,允许注册
				$insert_sql = 'insert into login (name, password) values ('. '"' . $username . '"' . ',' . '"' . $password . '"' . ')';
				//echo $insert_sql;
				if (!$result = mysql_query($insert_sql, $connect)) {	//mysql_query 执行失败
					throw new Exception('Mysql insert error: ' . mysql_error());

					// response message to client
					// TODO
					Response::show(502,'Mobile_Register: inset into database error');

					return false;
				} else {	//mysql_query 执行成功
					Response::show(500,'Mobile_Register: register successful');

					return true;
				}
			}
			else {	//名字存在，返回错误信息
				Response::show(503,'Mobile_Register: user name already exist');

				return false;
			}
		} else {
			Response::show('504','Mobile_Register: username or password is empty');
		}
	}
}

/* test
  * 测试用户注册
*/
/* 去掉namespace
require_once('/var/www/html/ant/Common/Db.php');
//require_once(BASEDIR . '/../../Common/Db.php');
// 生成数据库句柄
//$connect = Common\Db::getInstance()->connect();
try {
	$connect = Common\Db::getInstance()->connect();
} catch (Exception $e) {
	echo "error ocurrs: " , $e;
}

//$rg = new App/Register/Mobile_Register();
$rg = new Mobile_Register();
$rg->register('chendq', '123', $connect);
*/
