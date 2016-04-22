<?php
/*
 * Db.php
 * Description: use Singleton pattern to connect database, and returns a handle to control database
 *  Created on: 2015/4/20
 *      Author: Chen Deqing
 */

namespace Common;

class Db {
	static private $_instance;
	static private $_connectSource;

    // parameters to connect database
    private $user = 'ant';
    private $pwd = 'ant';
    private $svr = '192.168.146.88/mobile';

	//private $_dbConfig = array(
	//	'host' => '127.0.0.1',
	//	'user' => 'root',
	//	'password' => 'j88j,ui7i97',
	//	'database' => 'test',
	//	//'database' => 'ant',
	//);

	// 单例模式，构造函数声明为私有
	private function __construct() {
	}

	// 对外接口函数, 类内部生成对象实例
	static public function getInstance() {
		if(!(self::$_instance instanceof self)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function connect() {
		if(!self::$_connectSource) {
            self::$_connectSource = oci_connect($this->user,$this->pwd,$this->svr);

			if(!self::$_connectSource) {
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
			} 
			
		}
		return self::$_connectSource;
	}
}

/*

$connect = Db::getInstance()->connect();

$sql = "select * from login";
$result = mysql_query($sql, $connect);
echo mysql_num_rows($result);
var_dump($result);
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	printf ("ID: %s  Name: %s\n", $row["name"], $row["password"]);
}
*/
