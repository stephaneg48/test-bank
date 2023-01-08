<?php
   //print_r(json_decode($_POST['json'], true));

   $data = json_decode($_POST['json']);
   $data_out = json_encode($data);
   // var_dump($data_out);
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, "https://testbank-main.herokuapp.com/Architecture/Middle/create_q.php");
    curl_setopt($c, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($c, CURLOPT_POSTFIELDS, $data_out);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($c); 
    curl_close($c);
    
    $incoming_questions = $resp;
   
    echo $incoming_questions;

?>