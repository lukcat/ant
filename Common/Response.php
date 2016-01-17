<?php
/*
 * Response.php
 * Description: This module is mainly used to response data to client
 *  Created on: 2015/4/15
 *      Author: Chen Deqing
 */

namespace Common;

class Response {
	const JSON = "json";
	/**
	* 按综合方式输出通信数据
	* @param integer $code 状态码
	* @param string $message 提示信息
	* @param array $data 数据
	* @param string $type 数据类型
	* return string
	*/
	//public static function show($code, $message = '', $data = array(), $type = self::JSON) {
	public static function show($code, $message = '', $data = array(),$type = self::JSON) {
		if(!is_numeric($code)) {
			return '';
		}

		$type = isset($_GET['format']) ? $_GET['format'] : self::JSON;
        //var_dump($data);

		$result = array(
			'code' => $code,
			'message' => $message,
			'data' => $data
		);
        //var_dump($result);

		if($type == 'json') {
            //echo "json";
			self::json($code, $message, $data);
			exit;
		} elseif($type == 'array') {
			var_dump($result);
		} elseif($type == 'xml') {
			self::xmlEncode($code, $message, $data);
			exit;
		} else {
			// TODO
		}
	}

    // response without return 
	public static function echo_show($code, $message = '', $data = array(),$type = self::JSON) {
		if(!is_numeric($code)) {
			return '';
		}

		$type = isset($_GET['format']) ? $_GET['format'] : self::JSON;
        //var_dump($data);

		$result = array(
			'code' => $code,
			'message' => $message,
			'data' => $data
		);
        //var_dump($result);

		if($type == 'json') {
            //echo "json";
			self::json($code, $message, $data);
			//exit;
		} elseif($type == 'array') {
			var_dump($result);
		} elseif($type == 'xml') {
			self::xmlEncode($code, $message, $data);
			//exit;
		} else {
			// TODO
		}
	}
	/**
	* 按json方式输出通信数据
	* @param integer $code 状态码
	* @param string $message 提示信息
	* @param array $data 数据
	* return string
	*/
	public static function json($code, $message = '', $data = array()) {
		
		if(!is_numeric($code)) {
			return '';
		}

		$result = array(
			'code' => $code,
			'message' => $message,
			'data' => $data
		);

        // erase \ in front of /
        exit(str_replace("\\/","/", json_encode($result)));

        // for php version >= 5.4.0
		//exit(json_encode($result,JSON_UNESCAPED_SLASHES));

	}

	/**
	* 按xml方式输出通信数据
	* @param integer $code 状态码
	* @param string $message 提示信息
	* @param array $data 数据
	* return string
	*/
	public static function xmlEncode($code, $message, $data = array()) {
		if(!is_numeric($code)) {
			return '';
		}

		$result = array(
			'code' => $code,
			'message' => $message,
			'data' => $data,
		);

		header("Content-Type:text/xml");
		$xml = "<?xml version='1.0' encoding='UTF-8'?>\n";
		$xml .= "<root>\n";

		$xml .= self::xmlToEncode($result);

		$xml .= "</root>";
		//echo $xml;
		exit($xml);
	}

	public static function xmlToEncode($data) {

		$xml = $attr = "";
		foreach($data as $key => $value) {
			if(is_numeric($key)) {
				$attr = " id='{$key}'";
				$key = "item";
			}
			$xml .= "<{$key}{$attr}>";
			$xml .= is_array($value) ? self::xmlToEncode($value) : $value;
			$xml .= "</{$key}>\n";
		}
		return $xml;
	}

}
