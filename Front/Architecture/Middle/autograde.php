<?php

  $incoming_data = file_get_contents('php://input',true);
  //print_r($incoming_data);
  
  $c = curl_init();
  curl_setopt($c, CURLOPT_URL, "https://testbank-main.herokuapp.com/Architecture/Release/list_answers.php");
  
  curl_setopt($c, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
  curl_setopt($c, CURLOPT_POSTFIELDS, $incoming_data);
  curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
  $resp = curl_exec($c); 
  curl_close($c); 
  //echo $resp;

  $autograde_data = json_decode($resp, true);
  //var_dump($autograde_data);
  
  //pseudo values
  /*$autograde_data = [
    "questions"=>["Hi how are u", "what is up?", "here is exam"],
    "answers"=>["def doubleIt(a):\n\tb=a*2\n\treturn b;","def roundupfunction", "def thirdfunction"],
    "testcases"=>[
        ["test1"=>"5,10","test2"=>"10,20"],
        ["test1"=>"40","test2"=>"60"],
        ["test1"=>"80","test2"=>"100"]
    ],
    "points"=>["10","20","30"],
    "functionNames"=>["doubleIt","roundUp()","thirdFunction()"],
    "categories"=>["arithmetic", "for-loop", "if-statement"]
  ];*/
 
  $questions = array();
  $question_ids=array();
  $total_exam = 0;
  $total_exam_res = array();
  $name_res=array();
  $testcases_res = array();
  $constraint_res = array();
  $actual_points_perQ = array();
  for($i=0; $i<count($autograde_data); $i++)
  {
    $grades = [];
    $total = 0;
    $each_obj = $autograde_data[$i];
    //var_dump($each_obj);
    $question_id = $each_obj["question_id"];
    $question = $each_obj["question"];
    $student_answer = $each_obj["student_answer"];
    $testcases = array("test1"=>$each_obj['case_1'],"test2"=>$each_obj['case_2'],"test3"=>$each_obj['case_3'],"test4"=>$each_obj['case_4'],"test5"=>$each_obj['case_5']);
    $point = $each_obj['question_points'];
    $functionName = $each_obj['function'];
    $constraint = $each_obj['constraint'];
    
    array_push($actual_points_perQ, $point);
    
    //echo $constraint;
    
    if(strpos($functionName, '(') !== false)
      $functionName = substr($functionName, 0, strpos($functionName, "("));
    //echo $functionName;
    $name_points_result = checkName($functionName, $student_answer);
    //print_r($name_points_result);
    array_push($name_res, $name_points_result);
    $point-=5;
    $constraint_point = array();
    if($constraint!="None")
    {
      $constraint_point = checkConstraint($constraint, $student_answer);
      array_push($constraint_res, $constraint_point);
      $point-=5;
    }
    else
    {
      $constraint_point = array();
      array_push($constraint_res, $constraint_point);
    }
    
    //$point/=2;
    $total+=$name_points_result["points_received"];
    $total+=$constraint_point["points_received"];
    $testcase_results = checkTestCase($testcases, $student_answer, $point);
    array_push($testcases_res, $testcase_results);
    //var_dump($testcase_results);
    for($x = 1; $x<=count($testcase_results); $x++)
    {
      $total+=$testcase_results[$x-1]["case_".$x];
       //echo $testcase_results[0]["case_".$x];
    }
    $res = ["name_check"=>$name_points_result, "testcases_check"=>$testcase_results];
    array_push($grades, $res);
    array_push($questions, $question);
    array_push($question_ids, $question_id);
   
    $total_exam+=$total;
    //echo $total;
    array_push($total_exam_res, round($total,2)); 
    //$categories = $each_obj['categories'];
  }
  $total_exam=floor($total_exam);
  $out = array("question_ids"=>$question_ids, "questions"=>$questions,"name_check"=>$name_res, "constraint_check"=>$constraint_res, "test_case_r"=>$testcases_res,"total_perQ"=>$total_exam_res, "total"=>$total_exam, "question_point_value" => $actual_points_perQ);
  $output=json_encode($out);
  echo $output;
  
  function checkName($functionName, $student_answer)
  {
  
    $name = 5;
    $student_answer = ltrim($student_answer); //trim the white space from the beginning of the answer if any
    $student_answer_split = substr($student_answer, 0, strpos($student_answer, "("));
    $student_function_name = preg_replace("/def /", "", $student_answer_split); //split the string to get each word in the answer

    $name_result=array();
    
    if($functionName==$student_function_name)
    {
      $name_result = array("points_received"=>$name, "comment"=>"Student has correct function name");     
    }
    else
    {
      $name=0;
      $name_result = array("points_received"=>$name, "comment"=>"Student has incorrect function name");
    }
    return $name_result;
  
  }
  
  function checkConstraint($constraint, $student_answer)
  {
    //echo "here";
    $constraint_check = array();
    $student_answer = ltrim($student_answer); 
    $student_answer_split = preg_split("/\s+|\(|:/", $student_answer);
    //$student_answer_split = substr($student_answer, 0, strpos($student_answer, "("));
    //var_dump($student_answer_split);
    $student_function = $student_answer_split[1]; 
    if($constraint=="For")
    {
      $constraint_check = array("points_received"=>0, "comment"=>"Student has not included for loop");
    }
    else if($constraint=="While")
    {
      $constraint_check = array("points_received"=>0, "comment"=>"Student has not included while loop");
    }
    else if($constraint=="Recursion")
    {
      $constraint_check = array("points_received"=>0, "comment"=>"Student has not included recursion");
    }
    for($i=0; $i<count($student_answer_split); $i++)
    {
      if($constraint=="For")
      {
        if($student_answer_split[$i]=="for")
        {
          $constraint_check = array("points_received"=>5, "comment"=>"Student has included for loop");
        }
      }
      else if($constraint=="While")
      {
        if($student_answer_split[$i]=="while")
        {
          $constraint_check = array("points_received"=>5, "comment"=>"Student has included while loop");
        }
      }
      else if($constraint=="Recursion")
      {
        if($student_answer_split[$i]==$student_function)
        {
          $constraint_check = array("points_received"=>5, "comment"=>"Student performs recursion");
        }
      }
    }
    //var_dump($constraint_check);
    return $constraint_check;
  
  }
  
  function checkTestCase($testcase, $student_answer, $point_val)
  {
    $student_answer = ltrim($student_answer); //trim the white space from the beginning of the answer if any
    $error = false;
    /*$student_answer_colon = explode(":",$student_answer);
    $student_ans_inside = explode("\n",$student_answer_colon[1]);
    $new_student_ans = $student_answer_colon[0].":";
    foreach($student_ans_inside as $a)
    {
      $new_student_ans.=("\n\t".$a);
    }*/
    //echo "<br>$new_student_ans<br>";
    $split_answer = preg_split("/\s+|\(|:/", $student_answer);
    //$student_answer_split = substr($student_answer, 0, strpos($student_answer, "("));
    $student_function = $split_answer[1]; 
    
    $filename = "exam.py";
    
    $compile_cmd = 'python ./'.$filename;
    $compile_out = array();
    
    $testout = array();
    $testin = array();
    
    //var_dump($testcase);
    
    $test_num = 0;
    clearstatcache();
    file_put_contents($filename, $student_answer);
    
    for($i=1; $i<=count($testcase); $i++)
    {
      if($testcase["test".$i]!="")
      {
        $test_num++;
        $test = $testcase["test".$i];
        $paramOut = explode(",",$test);
        //var_dump($paramOut);
        $params = "";
        $exp_out = "";
        if(count($paramOut)==1)
          $exp_out=$paramOut[0];
        else
        {
          for($j=0; $j<count($paramOut)-2; $j++)
          {
            $params.=($paramOut[$j].",");
          }
          $params.=$paramOut[$j];
          $exp_out=$paramOut[count($paramOut)-1];
          array_push($testout, $exp_out);
          //var_dump($testout);
        }
        $params_in = "\nprint(".$student_function."(".$params.")".")";
        //$params_in =$student_function."(".$params.")".")";
        array_push($testin, $student_function."(".$params.")");
        //var_dump($testin);
        file_put_contents($filename,$params_in, FILE_APPEND); //appending function calls to python file
      }
    }
    
    //compile file
    exec($compile_cmd, $compile_out,$return_val);
    $compile_out[] = $return_val;
    //print_r($compile_out);
    
    $return_vals = array();
    /*for($i=0; $i<count($testin); $i++)
    {
      //file_put_contents($filename,$testin[$i], FILE_APPEND);
      $return_value=exec("python $filename"); //executing python file with student answer
      array_push($return_vals, $return_value);
      var_dump($return_vals);
    }*/
    
    exec("python $filename", $return_vals, $exec_val);
    
    $testcase_result = array();
    $test_p=array();
    //print_r(count($return_vals));
    if(count($return_vals)==0){
      for($i=1; $i<=count($testin); $i++)
      {
        $test_p=array("case_".$i=>0,"comments"=>"Test case ".$i." failed with input: ".$testin[$i-1]." and output: ".$testout[$i-1]." because there was an error in code");
        array_push($testcase_result, $test_p);
      }
    }
    else{
      for($i=1; $i<=count($return_vals); $i++)
      {
        if("".$return_vals[$i-1]==$testout[$i-1])
        {
          $test_p=array("case_".$i=>round(($point_val/$test_num),2),"comments"=>"Test case ".$i." passed with input: ".$testin[$i-1]." and output: ".$testout[$i-1]);
          //var_dump($t1_p);
        }
        else
        {
          $test_p=array("case_".$i=>0,"comments"=>"Test case ".$i." failed. The input: ".$testin[$i-1]." had expected output: ".$testout[$i-1]." but actual output was ".$return_vals[$i-1]);
        }
        //var_dump($test_p);
        array_push($testcase_result, $test_p);
      }
    }
    
    
   /* $test1 = $testcase["test1"];
    $test2 = $testcase["test2"];
    
    $testcase1 = explode(",",$test1);
    $testcase2 = explode(",",$test2);
    //var_dump($testcase);
    
    $testcase_input1=""; //params for testcase 1
    $testcase_output1=""; //resulting output for testcase 1
    $testcase_input2=""; //params for testcase 2
    $testcase_output2=""; //resulting output for testcase 2
    
    if(count($testcase1)==1)
      $testcase_output1=$testcase1[0];
    else
    {
      for($i=0; $i<count($testcase1)-2; $i++)
      {
        $testcase_input1.=($testcase1[$i].",");
      }
      $testcase_input1.=$testcase1[$i];
      $testcase_output1=$testcase1[count($testcase1)-1];
    }
    if(count($testcase2)==1)
      $testcase_output2=$testcase2[0];
    else
    {
      for($i=0; $i<count($testcase2)-2; $i++)
      {
        $testcase_input2.=($testcase2[$i].",");
      }
      $testcase_input2.=$testcase2[$i];
      $testcase_output2=$testcase2[count($testcase2)-1];
    }*/
    
    //$filename = "exam.py";
    //echo $student_function;
    
    //write to file
    /*clearstatcache();
    $params1 = "\nprint(".$student_function."(".$testcase_input1.")".")";
    $params2 = "\nprint(".$student_function."(".$testcase_input2.")".")";*/
    
    //echo "<br>$testcase_input1<br>";
    //echo "<br>$testcase_input2<br>";
    //file_put_contents($filename, $student_answer);
    //file_put_contents($filename,$params1, FILE_APPEND);
    //file_put_contents($filename, $params2, FILE_APPEND);
    //file_put_contents($filename, "\nprint($student_function$testcase_input1)", FILE_APPEND);
    //file_put_contents($filename, "\nprint($student_function$testcase_input2)", FILE_APPEND);
    /*
    $open_file = fopen($filename,'w') or die("Can not open file.");
    $file_input1 = $student_answer."\n"."print($student_function($testcase_input1))";
    fwrite($open_file,$file_input1);
    fclose($open_file);*/
    
    
    /*$return_vals = array();
    exec("python $filename", $return_vals, $exec_return);
    
    //echo shell_exec('python exam.py');
    
    //run file
    //$python_exec1 = exec("python $filename");
    //var_dump($return_vals);
    $testcase_result = array();
    $t1_p=array();
    $t2_p=array();
    if(count($return_vals)==2)
    {
      //var_dump($return_vals[0]);
      //var_dump($testcase_output1);
      if("$return_vals[0]"==$testcase_output1)
      {
        $t1_p=array("case_1"=>($point_val/$test_num),"comments"=>"Test case 1 passed");
        //var_dump($t1_p);
      }
      else
        $t1_p=array("case_1"=>0,"comments"=>"Test case 1 failed");
      
      if("$return_vals[1]"==$testcase_output2)
      {
        $t2_p=array("case_2"=>$point_val,"comments"=>"Test case 2 passed");
      }
      else
        $t2_p=array("case_2"=>0,"comments"=>"Test case 2 failed");
    }
    else
    {
      $t1_p=array("case_1"=>0,"comments"=>"Test case 1 failed");
      $t2_p=array("case_2"=>0,"comments"=>"Test case 2 failed");
    }
    
    array_push($testcase_result, $t1_p);
    array_push($testcase_result, $t2_p);*/
    
    //var_dump($testcase_result);
    return $testcase_result;
    
  }

?>