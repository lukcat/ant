<?php

function xml(){
    //$xml = simplexml_load_file($_SERVER ['DOCUMENT_ROOT']."/data/CityID.xml");//调用该文件
    $xml = simplexml_load_file("/var/www/html/ant/test/config.xml");
    $array=array();//定义数组
    foreach ($xml as $key => $value) { //遍历数组
        foreach($value as $key=>$val){
            foreach($val as $key=>$val){
                echo $key.':'.$val;
                $tmp = 
                //获取XML文件中的值
                /*
                $keys=$val->GEO_ID;
                $values=$val->GEO_NAME;
                $area = $val->OF_GEO;
                //组合成新的数组
                $array[]=$keys.'_'.$values;
                if($area=='(null)'){
                    $array[]='0_'.$keys.'_'.$values;
                }else{
                    $array[]='1_'.$keys.'_'.$values;
                }
                */ 
                //echo $key.':'.$val;
            }
        }
    }
    //return ($array);
}

xml();
//$data = xml();
//var_dump($data);
