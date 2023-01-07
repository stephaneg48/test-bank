<?php
  $incoming_data = file_get_contents('php://input',true);
  $data_decode = (array)json_decode($incoming_data);
  $URL="";
  $data_encode="";
  if(array_key_exists("sendResults",$data_decode)) //check if the request is coming from the create exam page
  {
    $data_decode = $data_decode["sendResults"];
    $data_encode = json_encode($data_decode);
    //print_r($data_decode);
    $URL="https://afsaccess4.njit.edu/~sag48/490/model/insert_results.php";
  }
  else //or view exam page
  {
    $URL = "https://afsaccess4.njit.edu/~sag48/490/model/list_exams_to_grade.php";
  }
  $c = curl_init();
  curl_setopt($c, CURLOPT_URL, $URL);
  curl_setopt($c, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
  curl_setopt($c, CURLOPT_POSTFIELDS, $data_encode);
  curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
  $resp = curl_exec($c); 
  curl_close($c); 
  
  echo $resp;
?>