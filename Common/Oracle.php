<?php
/*
 * Oralce.php
 * Description: use Singleton pattern to connect database, and returns a handle to control database
 *  Created on: 2015/7/1
 *      Author: Chen Deqing
 */

namespace Common;

class Oracle {
	static private $_instance;
	static private $_connectSource;

    // parameters to connect database
    private $user = 'ant';
    private $pwd = 'ant';
    private $svr = '192.168.1.200/mobile';

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
            //echo "before";
            self::$_connectSource = oci_connect($this->user,$this->pwd,$this->svr);
            //echo "after";

			if(!self::$_connectSource) {
                //echo "error occur in Common\Oracle.php";
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
			} 
			
		}
		return self::$_connectSource;
	}
}


/*
$connect = Oracle::getInstance()->connect();
$sql = "select * from app_user";
$res = oci_parse($connect,$sql);
if(!oci_execute($res)) {
    echo "exit";
}
if ($testrows = oci_fetch_array($res, OCI_BOTH)) {
    echo $testrows['NAME'];
}
*/

/*
$connect = Oracle::getInstance()->connect();

$sql = "select * from login";
$result = mysql_query($sql, $connect);
echo mysql_num_rows($result);
var_dump($result);
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	printf ("ID: %s  Name: %s\n", $row["name"], $row["password"]);
}
*/
