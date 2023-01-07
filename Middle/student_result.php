<?php

  //$incoming_data = file_get_contents('php://input',true);
  //$data_decode = (array)json_decode($incoming_data);
  
 /* $data = array(
    "exam_name" => "ExamName",
    'question_id' => array("10","34","86"),
    'exam_answers' => array("def math()","def sum()","def plusTen()"),
    'auto_result' => array(array("name_check"=>5, "testcase1_check"=>10, "testcase2_check"=>10),array("name_check"=>5, "testcase1_check"=>0, "testcase2_check"=>10),array("name_check"=>5, "testcase1_check"=>0, "testcase2_check"=>0)),
    'edit_result' => array(array("name_check"=>5, "testcase1_check"=>10, "testcase2_check"=>10),array("name_check"=>5, "testcase1_check"=>7, "testcase2_check"=>10),array("name_check"=>5, "testcase1_check"=>7, "testcase2_check"=>7)),
    'total_received' => 45
  );*/
  $incoming_data = file_get_contents('php://input',true);
  $data_decode = (array)json_decode($incoming_data);
  //print_r($data_decode);
  //print_r("here");
  $URL="";
  $data_encode="";
  if(array_key_exists("sendResults",$data_decode)) //check if the request is coming from the create exam page
  {
    $data_decode = $data_decode["sendResults"];
    print_r($data_decode);
    $data_encode = json_encode($data_decode);
    //print_r($data_decode);
    $URL="https://afsaccess4.njit.edu/~sag48/490/model/insert_scores.php";
  }
  else if(array_key_exists("request_exam",$data_decode))
  {
    $data_decode = $data_decode["request_exam"];
    $data_encode = json_encode($data_decode);
    $URL="https://afsaccess4.njit.edu/~sag48/490/model/list_student_grade.php";
  }
  else //or view exam page with all the fully graded exams on student side
  {
    $URL = "https://afsaccess4.njit.edu/~sag48/490/model/list_graded_exams.php";
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