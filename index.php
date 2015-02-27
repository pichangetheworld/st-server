<?php
/**
 * Created by PhpStorm.
 * User: pchan
 * Date: 15/02/26
 */

//$host = "192.168.211.131";
$host = "localhost";
$user = "pi";
$pass = "change*";
$db = "system_trump";

// Function to execute query
function execute_query($query) {

    $r = mysql_query($query);

    if (!$r) {
//        echo "Cannot execute query: $query\n";
        echo "Cannot execute query: $query<br>";
        trigger_error(mysql_error());
    } else {
        echo "Query: $query executed\n";
    }
}

// Start by connecting to MySQL
$r = mysql_connect($host, $user, $pass);

if (!$r) {
    echo "Could not connect to server\n";
    trigger_error(mysql_error(), E_USER_ERROR);
} else {
//    echo "Connection established\n";
    echo "Connection established<br>";
}

$r2 = mysql_select_db($db);

if (!$r2) {
    echo "Cannot select database\n";
    trigger_error(mysql_error(), E_USER_ERROR);
} else {
//    echo "Database ",$db," selected\n";
    echo "Database ",$db," selected<br>";
}

// dummy data
$uuid = 200;
$auth_type = "'goog'";
$facebook_id = 'NULL';
$google_id = "'24518789'";
$created_at = 'NULL'; // automatically created
$updated_at = 'NULL'; // automatically updated
$coins = 10;
$level = 1;

//$query = "INSERT INTO users VALUES($uuid,$auth_type,$facebook_id,$google_id,$created_at,$updated_at,$coins,$level)";
//execute_query($query);

// QUERY 1
//$query = "SELECT * FROM users LIMIT 5";

// QUERY 2
//$query = "SELECT uuid, auth_type, created_at FROM users LIMIT 5";

// QUERY 3
$name = 200;
$query = sprintf("SELECT uuid, auth_type, created_at From users Where uuid = $name",
    mysql_real_escape_string($name));

$rs = mysql_query($query);

if (!$rs) {
    echo "Could not execute query: $query";
    trigger_error(mysql_error(), E_USER_ERROR);
} else {
//    echo "Query: $query executed\n";
    echo "Query: $query executed<br>";
}

// QUERY 1
//while ($row = mysql_fetch_assoc($rs)) {
//    echo "uuid:" . $row['uuid'] . " auth_type:" . $row['auth_type'] . " created at:" . $row['created_at'] . "<br>";
//}

// QUERY 2, 3
$nrows = mysql_num_rows($rs);

for ($i = 0; $i < $nrows; $i++) {
    $row = mysql_fetch_row($rs);
    echo "uuid:";
    echo $row[0];
    echo " auth_type:";
    echo $row[1];
    echo " created at:";
    echo $row[2];
    echo "<br>";
}

// QUERY 3
mysql_close();

?>