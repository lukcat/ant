<?php

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

$stid = oci_parse($conn, 'select table_name from user_tables');

if(oci_execute($stid)) {
    echo "execute OK";
}

while (($row = oci_fetch_assoc($stid)) != false) {
    echo $row['TABLE_NAME']."\n";
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
