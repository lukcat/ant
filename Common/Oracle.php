<?php
/*
 * Oralce.php
 * Description: use Singleton pattern to connect database, and returns a handle to control database
 *  Created on: 2015/7/1
 *      Author: Chen Deqing
 */

namespace Common;

use Common\Response as Response;

class Oracle {
	//static private $_mobileInstance;
	//static private $_bgdataInstance;
	//static private $_connectSource;
	private $_instance;
	private $_connectSource;

    // parameters to connect database
    //private $user = 'ant';
    //private $pwd = 'ant';
    //private $svr = '172.16.0.49/mobile';
    //private $svr = '192.168.146.88/mobile';

	// 单例模式，构造函数声明为私有
	private function __construct() {
	}

	// 对外接口函数, 类内部生成对象实例
	static public function getInstance() {
		//if(!(self::$_instance instanceof self)) {
			//self::$_instance = new self();
            //$this->_instance = new self();
            $_instance = new self();
		//}
		//return self::$_instance;
		//return $this->_instance;
		return $_instance;
	}

	//public function connect() {
	//public function connect($hostname,$instance,$username,$password) {
	public function connect($serverInfo) {
        $hostname = $serverInfo['hostname'];
        $instance = $serverInfo['instance'];
        $username = $serverInfo['username'];
        $password = $serverInfo['password'];

		//if(!self::$_connectSource) {
		//if(!$_connectSource) {
            $server = $hostname . "/" . $instance;
            //self::$_connectSource = oci_connect($username,$password,$server);
            $_connectSource = oci_connect($username,$password,$server);

			//if(!self::$_connectSource) {
			if(!$_connectSource) {
                Response::show(101,"Database connect error, please start your listener and instance");
			} 
			
		//}
		return $_connectSource;
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
