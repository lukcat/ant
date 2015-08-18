<?php

namespace Common;

class Config {

    function getXml($path){
        //$xml = simplexml_load_file($_SERVER ['DOCUMENT_ROOT']."/data/CityID.xml");//调用该文件
        //$xml = simplexml_load_file("/var/www/html/ant/test/sys_config.xml");

        $xml = simplexml_load_file(realpath($path));
        $i=1;

        foreach ($xml as $key1 => $value1) { //遍历数组
            $configInfo[$key1] = preg_replace("/\s/","",(string)$value1);
            foreach($value1 as $key2=>$value2){
                $configInfo[$key1][$key2] = preg_replace("/\s/","",(string)$value2);
                //echo $i.' '.$key2;
                //$i++;
                foreach($value2 as $key3=>$value3){
                    $configInfo[$key1][$key2][$key3] = preg_replace("/\s/","",(string)$value3);

                }
            }
        }
        //var_dump($configInfo);
        return ($configInfo);
    }
}

//getXml();
//$data = xml();
//var_dump($data);

