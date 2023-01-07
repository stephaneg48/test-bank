<?php
/*
	$ucid = "";
    $password = "";
	if(empty($_POST["ucid"])){
		header('Location: ./login.php');
	}
	if(empty($_POST["password"])){
		header('Location: ./login.php');
	}

	$ucid = $_POST["ucid"];
	$password = $_POST["password"];

	$c_url = "https://afsaccess4.njit.edu/~mjv43/CS490/Controller_Project/login_info.php";
	
	$ch = curl_init($c_url);
	$data = array(
				"ucid" => $ucid,
				"password" => $password
            );
				
	$payload = json_encode($data);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);
	
	echo $result;
	
?>

<?php

if(isset($_POST['ucid']))
    $ucid = $_POST['ucid']; 

if(isset($_POST['password']))
    $password = $_POST['password'];

$data = array(
    'ucid'=>$ucid, 
    'password'=>$password
);

echo $data['ucid'];

$to_string = http_build_query($data);
$c = curl_init();
curl_setopt($c, CURLOPT_URL, "https://afsaccess4.njit.edu/~sag48/490/model/login.php");
curl_setopt($c, CURLOPT_POST, true);
curl_setopt($c, CURLOPT_POSTFIELDS, $to_string);
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
$resp = curl_exec($c); 
curl_close($c); 
//$jsonDecoded = json_decode($resp);
//echo $jsonDecoded; 
echo $resp; */

$ucid="pw92";
$password="tennis"; 

$hash = password_hash($password, PASSWORD_BCRYPT);
echo $hash;

?>