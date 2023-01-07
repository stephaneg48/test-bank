<?php

  $incoming_data = file_get_contents('php://input',true);
  $c = curl_init();
  error_log("incoming data: " . $incoming_data);
  curl_setopt($c, CURLOPT_URL, "/../Release/login.php");
  curl_setopt($c, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
  curl_setopt($c, CURLOPT_POSTFIELDS, $incoming_data);
  curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
  $resp = curl_exec($c); 
  curl_close($c); 
  echo $resp;

  $incoming_data_two = file_get_contents('php://input',true);
  echo $incoming_data_two;

?>