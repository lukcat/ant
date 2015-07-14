<?php

// Connects to the XE service (i.e. database) on the "localhost" machine
//$conn = oci_connect('ant', 'ant', '192.168.146.88/mobile');
$conn = oci_connect('ant2', 'ant2', '192.168.146.88/mobile');
if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

$sqlcreate = "CREATE TABLE HELLO3(NAME VARCHAR2(255)) ";
//$sqlinsert = "INSERT INTO HELLO1(NAME) values ('chendq')";
$stid = oci_parse($conn, $sqlcreate);
//$stid = oci_parse($conn, $sqlinsert);
//$stid = oci_parse($conn, 'SELECT * FROM employees');
$res = oci_execute($stid);
if ($res) {
    echo "OK";
} else {
    echo "FALSE";
}
//
//echo "<table border='1'>\n";
//while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {

//    foreach ($row as $item) {
//        echo "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
//    }
//    echo "</tr>\n";
//}
//echo "</table>\n";
//
//



//$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.146.88)(PORT = 1521)))(CONNECT_DATA=(SID=mobile)))"; 
//
//$c1 = oci_connect("ant", "ant", $db);
//
//if ($c1) {
//    echo "successful\n";
//} else {
//    echo "falure\n";
//}
//
//function create_table($conn)
//{
//      $stmt = oci_parse($conn, "create table hallo1(test varchar2(64))");
//      oci_execute($stmt);
//      echo $conn . " created table\n\n";
//}
//create_table($c1);


//insert_data($c1);   // Insert a row using c1
//insert_data($c2);   // Insert a row using c2

//function drop_table($conn)
//{
//      $stmt = oci_parse($conn, "drop table scott.hallo");
//        oci_execute($stmt);
//          echo $conn . " dropped table\n\n";
//}
//
//function insert_data($conn)
//{
//      $stmt = oci_parse($conn, "insert into scott.hallo
//                          values('$conn' || ' ' || to_char(sysdate,'DD-MON-YY HH24:MI:SS'))");
//        oci_execute($stmt, OCI_DEFAULT);
//          echo $conn . " inserted hallo\n\n";
//}
//
//function delete_data($conn)
//{
//      $stmt = oci_parse($conn, "delete from scott.hallo");
//        oci_execute($stmt, OCI_DEFAULT);
//          echo $conn . " deleted hallo\n\n";
//}
//
//function commit($conn)
//{
//      oci_commit($conn);
//        echo $conn . " committed\n\n";
//}
//
//function rollback($conn)
//{
//      oci_rollback($conn);
//        echo $conn . " rollback\n\n";
//}
//
//function select_data($conn)
//{
//      $stmt = oci_parse($conn, "select * from scott.hallo");
//        oci_execute($stmt, OCI_DEFAULT);
//          echo $conn."----selecting\n\n";
//            while (oci_fetch($stmt)) {
//                    echo $conn . " [" . oci_result($stmt, "TEST") . "]\n\n";
//                      }
//              echo $conn . "----done\n\n";
//}
//
//create_table($c1);
//insert_data($c1);   // Insert a row using c1
//insert_data($c2);   // Insert a row using c2
//
//select_data($c1);   // Results of both inserts are returned
//select_data($c2);
//
//rollback($c1);      // Rollback using c1
//
//select_data($c1);   // Both inserts have been rolled back
//select_data($c2);
//
//insert_data($c2);   // Insert a row using c2
//commit($c2);        // Commit using c2
//
//select_data($c1);   // Result of c2 insert is returned
//
//delete_data($c1);   // Delete all rows in table using c1
//select_data($c1);   // No rows returned
//select_data($c2);   // No rows returned
//commit($c1);        // Commit using c1
//
//select_data($c1);   // No rows returned
//select_data($c2);   // No rows returned
//
//drop_table($c1);
//echo "</pre>";
