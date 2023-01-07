<?php 
    $data = [];
    if(isset($_POST['json'])){
    //  print_r(json_decode($_POST['json'], true));
        $data = json_decode($_POST['json']);
        //print_r(json_decode($_POST['json'], true));

    }
    if(!array_key_exists("sendResults",$data) && !array_key_exists("request_exam",$data)){
        $data = '';
    }
    //print_r($data);
    $data_out = json_encode($data);

    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, "https://afsaccess4.njit.edu/~mjv43/CS490/Beta/student_result.php");
    curl_setopt($c, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($c, CURLOPT_POSTFIELDS, $data_out);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($c); 
    curl_close($c);
    
    $teacherRubric = $resp;
   
    echo $teacherRubric;
?>