<?php
date_default_timezone_set('UTC');

//echo "ssss\n";
$conn = oci_connect("ant", "ant", "192.168.146.88/mobile");

if(!$conn) {
    $e = oci_error();
    //trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    print_r($e);

    //echo "conn is not valid";
} else {
    echo "conn is OK\n";
}

$sql = "SELECT to_char(CREATE_TIME,'yyyy-mm-dd hh24:mi:ss') AS CREATE_TIME FROM APP_USER WHERE LOGIN_NAME='cdqing'";

//$stid = oci_parse($conn, 'select table_name from user_tables');
$stid = oci_parse($conn, $sql);

if(!oci_execute($stid)) {
    echo "execute failure";
}

while (($row = oci_fetch_assoc($stid)) != false) {
    //echo $row['TABLE_NAME']."\n";

    /*
    $time = $row['CREATE_TIME'];
    echo $time;

    $realtime = strtotime($time);
    echo $realtime;

    

    //$plustime = $realtime + 3600;
    echo $realtime;
    */

    $curtime = new DateTime();
    $curtimestr = $curtime->format('Y-m-d H:i:s');
    
    echo $curtimestr.'\n';
    $curtimestr = $curtime->modify('+2 day');
    $curtimestr = $curtime->format('Y-m-d H:i:s');
    echo strtotime($curtime);


    $test = "cdqchendeqing@ceiec.com.cn123";
    $sn = sha1(md5($test));
    echo $sn;

    //echo date('Y-m-d H:i:s');

}

/*
echo "<table>\n";
while (($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
    echo "<tr>\n";
    foreach ($row as $item) {
        echo "  <td>".($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;")."</td>\n";
    }
    echo "</tr>\n";
}
echo "</table>\n";

?>
*/
