<?php

$db_host = "sql1.njit.edu"; // server hosting database
$db_user = "sag48";         // name of user account
$db_name = "sag48";         // name of database from user account
$db_password = "FlesKM1zarU4%@";

$db = mysqli_connect($db_host, $db_user, $db_password, $db_name);

if (mysqli_connect_errno())
{ 
  echo "Failed to connect to MySQL: " . mysqli_connect_error($db);
  exit();
}

mysqli_select_db($db, $db_name); // makes db_name the default

?>


