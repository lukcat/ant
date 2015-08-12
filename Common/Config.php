<?php

namespace Common;

class Config {

    function getXml($path){
        //$xml = simplexml_load_file($_SERVER ['DOCUMENT_ROOT']."/data/CityID.xml");//调用该文件
        //$xml = simplexml_load_file("/var/www/html/ant/test/sys_config.xml");

        $xml = simplexml_load_file(realpath($path));

        foreach ($xml as $key1 => $value1) { //遍历数组
            foreach($value1 as $key2=>$value2){
                foreach($value2 as $key3=>$value3){
                    $configInfo[$key1][$key2][$key3] = preg_replace("/\s/","",(string)$value3);

                }
            }
        }
        return ($configInfo);
        //var_dump($configInfo);
    }
}

//getXml();
//$data = xml();
//var_dump($data);

