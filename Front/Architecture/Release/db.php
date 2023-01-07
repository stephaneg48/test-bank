<?php

// fixed 1/7/23

$ini = @parse_ini_file("lib/.env");

if($ini && isset($ini["JAWSDB_URL"])){
    //load local .env file
    $db_url = parse_url($ini["JAWSDB_URL"]);
    $db_url["path"] = ltrim($db_url["path"], "/");
}
else{
    //load from heroku env variables
    $db_url = parse_url(getenv("JAWSDB_URL"));
    $db_url["path"] = ltrim($db_url["path"], "/"); // must trim for heroku
}

$db_host = $db_url["host"]; // server hosting database
$db_user = $db_url["user"]; // name of user account
$db_name = substr($db_url["path"],1); // name of database from user account
$db_password = $db_url["pass"];


$db = mysqli_connect($db_host, $db_user, $db_password, $db_name);

if (mysqli_connect_errno())
{ 
  echo "Failed to connect to MySQL: " . mysqli_connect_error($db);
  exit();
}
else
{
  error_log("Successfully connected to database...");
}


mysqli_select_db($db, $db_name); // makes db_name the default

?>


