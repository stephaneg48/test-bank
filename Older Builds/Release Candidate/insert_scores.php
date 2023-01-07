<?php
require_once(__DIR__ . "/db.php");
?>

<?php

$data = array();

// goal: receive data sent from front and place ALL required parts of it into DB

// auto_result: an array of arrays that has the points that the student earned for each question based on each check
// edit_result: ditto, but modified, if necessary

/*if(isset($decoded_data['user_role'])) // once it actually works, uncomment this
    $user_role = $decoded_data['user_role'];
else 
    {
        echo "You must be logged in and have permission to view this page.";
        exit();
    }
*/

$incoming_data = file_get_contents("php://input");

$decoded_data = json_decode($incoming_data, true);

if(!empty($decoded_data)) // if it's empty, then it will use the test data above, which means something is wrong
    $student_exam_results = $decoded_data;

// set all the data that was just sent from the decoded data...


$exam_name = $student_exam_results['examName']; // key-value
$autograde_result = $student_exam_results['autograde']; // array of arrays
$edited_result = $student_exam_results['edit_results']; // array of arrays
$final_points = $student_exam_results['examTotal']; // key-value

// end setting

$responses_ids = array(); // student_answer_id
$auto_question_points = array();
$auto_func_name_points = array();
$auto_constraint_points = array();
$auto_case_1_points = array(); // "case 1 for question 1, case 2 for question 2, ..."
$auto_case_2_points = array(); // "case 2 for question 1, case 2 for question 2, ..."
$auto_case_3_points = array();
$auto_case_4_points = array();
$auto_case_5_points = array();

$question_points_values = array();

$auto_func_name_comments = array();
$auto_constraint_comments = array();
$auto_case_1_comments = array();
$auto_case_2_comments = array();
$auto_case_3_comments = array();
$auto_case_4_comments = array();
$auto_case_5_comments = array();

$q_ids = $autograde_result['question_ids']; // array
$questions = $autograde_result['questions']; // array... don't need this data?

$student_points_for_constraint = $autograde_result['constraint_check'];
$student_points_for_namecheck = $autograde_result['name_check']; // array of arrays (each array is the respective question)
$student_points_for_testcases = $autograde_result['test_case_r']; // array of arrays (each array is the respective question)
$student_points_per_question = $autograde_result['total_perQ']; // array

// get auto_func_name_points from $student_points_for_namecheck
// get auto_case_1_points and auto_case_2_points from $student_points_for_testcases
// get auto_question_points from $student_points_per_question

for($i = 0; $i < count($q_ids);$i++)
{
    $current_question_for_namecheck = $student_points_for_namecheck[$i];
    $auto_func_name_comments[] = $current_question_for_namecheck['comment'];
    $auto_func_name_points[] = $current_question_for_namecheck['points_received'];
    
    $current_question_for_constraint = $student_points_for_constraint[$i];
    if(empty($current_question_for_constraint['comment']))
    {
        $auto_constraint_comments[] = "";
        $auto_constraint_points[] = 0;
    }
    else
    {
        $auto_constraint_comments[] = $current_question_for_constraint['comment'];
        $auto_constraint_points[] = $current_question_for_constraint['points_received'];
    }
    
    $auto_question_points[] = $student_points_per_question[$i];

    $current_question_for_testcases = $student_points_for_testcases[$i]; // literally the question itself
    
    $counter = 1;
    for ($j = 0; $j < count($current_question_for_testcases); $j++)
    {
        $current_testcase = $current_question_for_testcases[$j];

        if ($counter == 1)
        {
            $auto_case_1_points[] = $current_testcase['case_1'];
            $auto_case_1_comments[] = $current_testcase['comments'];
            $counter += 1;
        }
        else if ($counter == 2)
        {
            $auto_case_2_points[] = $current_testcase['case_2'];
            $auto_case_2_comments[] = $current_testcase['comments'];
            $counter += 1;
        }
        else if ($counter == 3)
        {
            $auto_case_3_points[] = $current_testcase['case_3'];
            $auto_case_3_comments[] = $current_testcase['comments'];
            $counter += 1;
        }
        else if ($counter == 4)
        {
            $auto_case_4_points[] = $current_testcase['case_4'];
            $auto_case_4_comments[] = $current_testcase['comments'];
            $counter += 1;
        }
        else if ($counter == 5)
        {
            $auto_case_5_points[] = $current_testcase['case_5'];
            $auto_case_5_comments[] = $current_testcase['comments'];
            $counter += 1;
        }
    }
}

$edited_name_check_points = $edited_result['nameEdit']; // array of points earned for name check for each question
$edited_constraint_points = $edited_result['constraintEdit'];
$edited_testcase1_check_points = $edited_result['test1_edit']; // array of points earned for test case 1 check for each question
$edited_testcase2_check_points = $edited_result['test2_edit']; // array of points earned for test case 2 check for each question
$edited_testcase3_check_points = $edited_result['test3_edit']; // array of points earned for test case 3 check for each question
$edited_testcase4_check_points = $edited_result['test4_edit']; // array of points earned for test case 4 check for each question
$edited_testcase5_check_points = $edited_result['test5_edit']; // array of points earned for test case 5 check for each question
$edited_question_points = $edited_result['total_perQ_edit'];
$comments = $edited_result['comments_edit'];

// 3. already have autograde_answer_points
// 4. already have edited_answer_points
// 5. insert into ExamResults
// 5.1. use edited_answer_points to store question_points (sum of one question's points accumulated) and the individual check's points

foreach($q_ids as $q_id) 
{
    // 1. use the question ids and the exam name to get the student's responses to each question AND the id of the student response
    $sql_2="SELECT id,student_answer FROM StudentAnswers WHERE exam_name='$exam_name' AND question_id=$q_id"; 
    $query_2=mysqli_query($db,$sql_2);
    while($result = mysqli_fetch_assoc($query_2))
    {
        $responses[] = $result['student_answer'];
        $responses_ids[] = $result['id'];
    } 
    // 2. use each question id and response id to update the studentanswers table with the teacher's respective comments for each question
    for($i = 0; $i < count($responses_ids); $i++)
    {
        $current_comment = $comments[$i];
        $current_response_id = $responses_ids[$i];
        //$sql_3="UPDATE StudentAnswers SET comments = '$current_comment' WHERE exam_name='$exam_name' AND question_id=$q_id AND id=$current_response_id";
        //$query_3=mysqli_query($db,$sql_3) or die("there was a problem with the given data: " . mysqli_error($db));
    }
}

for($i = 0; $i < count($questions); $i++) // for 1 exam, N question ids, N answer ids, N responses, ...
{
    $current_question_id = $q_ids[$i];
    // exam name...
    $current_student_answer_id = $responses_ids[$i];

    $current_question_response_autograde_points = $auto_question_points[$i]; // total per question
    $current_question_func_name_autograde_points = $auto_func_name_points[$i];
    $current_question_func_name_autograde_comment = $auto_func_name_comments[$i];

    $current_question_case_1_autograde_points = $auto_case_1_points[$i];
    $current_question_case_1_autograde_comment = $auto_case_1_comments[$i];
    $current_question_case_2_autograde_points = $auto_case_2_points[$i];
    $current_question_case_2_autograde_comment = $auto_case_2_comments[$i];

    // if the comment does not exist, then it can't have point value... 
    // ... so don't try and add the following placeholders into those columns for the query!
    
    $current_question_constraint_autograde_points = 0;
    $current_question_constraint_autograde_comment = "";
    $current_question_case_3_autograde_points = 0;
    $current_question_case_3_autograde_comment = "";
    $current_question_case_4_autograde_points = 0;
    $current_question_case_4_autograde_comment = "";
    $current_question_case_5_autograde_points = 0;
    $current_question_case_5_autograde_comment = "";

    if(!empty($auto_case_3_points[$i]))
        $current_question_case_3_autograde_points = $auto_case_3_points[$i];
    if(!empty($auto_case_3_comments[$i]))
        $current_question_case_3_autograde_comment = $auto_case_3_comments[$i];
    if(!empty($auto_case_4_points[$i]))
        $current_question_case_4_autograde_points = $auto_case_4_points[$i];
    if(!empty($auto_case_4_comments[$i]))
        $current_question_case_4_autograde_comment = $auto_case_4_comments[$i];
    if(!empty($auto_case_5_points[$i]))
        $current_question_case_5_autograde_points = $auto_case_5_points[$i];
    if(!empty($auto_case_5_comments[$i]))
        $current_question_case_5_autograde_comment = $auto_case_5_comments[$i];
    if(!empty($auto_constraint_points[$i]))
        $current_question_constraint_autograde_points = $auto_constraint_points[$i];
    if(!empty($auto_constraint_comments[$i]))
        $current_question_constraint_autograde_comment = $auto_constraint_comments[$i];

    $current_question_response_edit_points = $edited_question_points[$i]; // total per question
    $current_question_func_name_edit_points = $edited_name_check_points[$i];
    
    $current_question_case_1_edit_points = $edited_testcase1_check_points[$i];
    $current_question_case_2_edit_points = $edited_testcase2_check_points[$i];

    $current_question_constraint_edit_points = 0;
    $current_question_case_3_edit_points = 0;
    $current_question_case_4_edit_points = 0;
    $current_question_case_5_edit_points = 0;

    if(!empty($edited_constraint_points[$i]))
        $current_question_constraint_edit_points = $edited_constraint_points[$i];
    if(!empty($edited_testcase3_check_points[$i]))
        $current_question_case_3_edit_points = $edited_testcase3_check_points[$i];
    if(!empty($edited_testcase4_check_points[$i]))
        $current_question_case_4_edit_points = $edited_testcase4_check_points[$i];
    if(!empty($edited_testcase5_check_points[$i]))
        $current_question_case_5_edit_points = $edited_testcase5_check_points[$i];

    $sql_n = "";

    // easier to count down than up...
    // if case 5 isn't empty, include everything
    // else if case 4 isn't empty, include everything up to case 4
    // else if case 3 isn't empty, include everything up to case 3
    // else... then there are only two test cases... default (minimum)

    // edit: way more to check...
    // 5 test cases + constraint
    // 5 test cases + no constraint
    // 4 test cases + constraint
    // 4 test cases + no constraint
    // 3 test cases + constraint
    // 3 test cases + no constraint
    // 2 test cases + constraint
    // 2 test cases + no constraint


    if(!empty($current_question_case_5_autograde_comment) && !empty($current_question_constraint_autograde_comment)) 
    {
        $sql_n="INSERT INTO ExamResults 
        (question_id,
        exam_name,
        student_answer_id,
        auto_question_points,
        auto_func_name_points,
        auto_func_name_comment,
        auto_constraint_points,
        auto_constraint_comment,
        auto_case_1_points,
        auto_case_1_comment,
        auto_case_2_points,
        auto_case_2_comment,
        auto_case_3_points,
        auto_case_3_comment,
        auto_case_4_points,
        auto_case_4_comment,
        auto_case_5_points,
        auto_case_5_comment,
        edit_question_points,
        edit_func_name_points,
        edit_constraint_points,
        edit_case_1_points,
        edit_case_2_points,
        edit_case_3_points,
        edit_case_4_points,
        edit_case_5_points) 
        VALUES
        ($current_question_id,
        '$exam_name',
        $current_student_answer_id,
        $current_question_response_autograde_points,
        $current_question_func_name_autograde_points,
        '$current_question_func_name_autograde_comment',
        $current_question_constraint_autograde_points,
        '$current_question_constraint_autograde_comment',
        $current_question_case_1_autograde_points,
        '$current_question_case_1_autograde_comment',
        $current_question_case_2_autograde_points,
        '$current_question_case_2_autograde_comment',
        $current_question_case_3_autograde_points,
        '$current_question_case_3_autograde_comment',
        $current_question_case_4_autograde_points,
        '$current_question_case_4_autograde_comment',
        $current_question_case_5_autograde_points,
        '$current_question_case_5_autograde_comment',
        $current_question_response_edit_points,
        $current_question_func_name_edit_points,
        $current_question_constraint_edit_points,
        $current_question_case_1_edit_points,
        $current_question_case_2_edit_points,
        $current_question_case_3_edit_points,
        $current_question_case_4_edit_points,
        $current_question_case_5_edit_points)";
    }

    else if(!empty($current_question_case_5_autograde_comment) && empty($current_question_constraint_autograde_comment)) 
    {
        $sql_n="INSERT INTO ExamResults 
        (question_id,
        exam_name,
        student_answer_id,
        auto_question_points,
        auto_func_name_points,
        auto_func_name_comment,
        auto_case_1_points,
        auto_case_1_comment,
        auto_case_2_points,
        auto_case_2_comment,
        auto_case_3_points,
        auto_case_3_comment,
        auto_case_4_points,
        auto_case_4_comment,
        auto_case_5_points,
        auto_case_5_comment,
        edit_question_points,
        edit_func_name_points,
        edit_case_1_points,
        edit_case_2_points,
        edit_case_3_points,
        edit_case_4_points,
        edit_case_5_points) 
        VALUES
        ($current_question_id,
        '$exam_name',
        $current_student_answer_id,
        $current_question_response_autograde_points,
        $current_question_func_name_autograde_points,
        '$current_question_func_name_autograde_comment',
        $current_question_case_1_autograde_points,
        '$current_question_case_1_autograde_comment',
        $current_question_case_2_autograde_points,
        '$current_question_case_2_autograde_comment',
        $current_question_case_3_autograde_points,
        '$current_question_case_3_autograde_comment',
        $current_question_case_4_autograde_points,
        '$current_question_case_4_autograde_comment',
        $current_question_case_5_autograde_points,
        '$current_question_case_5_autograde_comment',
        $current_question_response_edit_points,
        $current_question_func_name_edit_points,
        $current_question_case_1_edit_points,
        $current_question_case_2_edit_points,
        $current_question_case_3_edit_points,
        $current_question_case_4_edit_points,
        $current_question_case_5_edit_points)";
    }

    else if(!empty($current_question_case_4_autograde_comment) && !empty($current_question_constraint_autograde_comment)) 
    {
        $sql_n="INSERT INTO ExamResults 
        (question_id,
        exam_name,
        student_answer_id,
        auto_question_points,
        auto_func_name_points,
        auto_func_name_comment,
        auto_constraint_points,
        auto_constraint_comment,
        auto_case_1_points,
        auto_case_1_comment,
        auto_case_2_points,
        auto_case_2_comment,
        auto_case_3_points,
        auto_case_3_comment,
        auto_case_4_points,
        auto_case_4_comment,
        edit_question_points,
        edit_func_name_points,
        edit_constraint_points,
        edit_case_1_points,
        edit_case_2_points,
        edit_case_3_points,
        edit_case_4_points) 
        VALUES
        ($current_question_id,
        '$exam_name',
        $current_student_answer_id,
        $current_question_response_autograde_points,
        $current_question_func_name_autograde_points,
        '$current_question_func_name_autograde_comment',
        $current_question_constraint_autograde_points,
        '$current_question_constraint_autograde_comment',
        $current_question_case_1_autograde_points,
        '$current_question_case_1_autograde_comment',
        $current_question_case_2_autograde_points,
        '$current_question_case_2_autograde_comment',
        $current_question_case_3_autograde_points,
        '$current_question_case_3_autograde_comment',
        $current_question_case_4_autograde_points,
        '$current_question_case_4_autograde_comment',
        $current_question_response_edit_points,
        $current_question_func_name_edit_points,
        $current_question_constraint_edit_points,
        $current_question_case_1_edit_points,
        $current_question_case_2_edit_points,
        $current_question_case_3_edit_points,
        $current_question_case_4_edit_points)";
    }

    else if(!empty($current_question_case_4_autograde_comment) && empty($current_question_constraint_autograde_comment)) 
    {
        $sql_n="INSERT INTO ExamResults 
        (question_id,
        exam_name,
        student_answer_id,
        auto_question_points,
        auto_func_name_points,
        auto_func_name_comment,
        auto_case_1_points,
        auto_case_1_comment,
        auto_case_2_points,
        auto_case_2_comment,
        auto_case_3_points,
        auto_case_3_comment,
        auto_case_4_points,
        auto_case_4_comment,
        edit_question_points,
        edit_func_name_points,
        edit_case_1_points,
        edit_case_2_points,
        edit_case_3_points,
        edit_case_4_points) 
        VALUES
        ($current_question_id,
        '$exam_name',
        $current_student_answer_id,
        $current_question_response_autograde_points,
        $current_question_func_name_autograde_points,
        '$current_question_func_name_autograde_comment',
        $current_question_case_1_autograde_points,
        '$current_question_case_1_autograde_comment',
        $current_question_case_2_autograde_points,
        '$current_question_case_2_autograde_comment',
        $current_question_case_3_autograde_points,
        '$current_question_case_3_autograde_comment',
        $current_question_case_4_autograde_points,
        '$current_question_case_4_autograde_comment',
        $current_question_response_edit_points,
        $current_question_func_name_edit_points,
        $current_question_case_1_edit_points,
        $current_question_case_2_edit_points,
        $current_question_case_3_edit_points,
        $current_question_case_4_edit_points)";
    }

    else if(!empty($current_question_case_3_autograde_comment) && !empty($current_question_constraint_autograde_comment)) 
    {
        $sql_n="INSERT INTO ExamResults 
        (question_id,
        exam_name,
        student_answer_id,
        auto_question_points,
        auto_func_name_points,
        auto_func_name_comment,
        auto_constraint_points,
        auto_constraint_comment,
        auto_case_1_points,
        auto_case_1_comment,
        auto_case_2_points,
        auto_case_2_comment,
        auto_case_3_points,
        auto_case_3_comment,
        edit_question_points,
        edit_func_name_points,
        edit_constraint_points,
        edit_case_1_points,
        edit_case_2_points,
        edit_case_3_points) 
        VALUES
        ($current_question_id,
        '$exam_name',
        $current_student_answer_id,
        $current_question_response_autograde_points,
        $current_question_func_name_autograde_points,
        '$current_question_func_name_autograde_comment',
        $current_question_constraint_autograde_points,
        '$current_question_constraint_autograde_comment',
        $current_question_case_1_autograde_points,
        '$current_question_case_1_autograde_comment',
        $current_question_case_2_autograde_points,
        '$current_question_case_2_autograde_comment',
        $current_question_case_3_autograde_points,
        '$current_question_case_3_autograde_comment',
        $current_question_response_edit_points,
        $current_question_func_name_edit_points,
        $current_question_constraint_edit_points,
        $current_question_case_1_edit_points,
        $current_question_case_2_edit_points,
        $current_question_case_3_edit_points)";
    }

    else if(!empty($current_question_case_3_autograde_comment) && empty($current_question_constraint_autograde_comment)) 
    {
        $sql_n="INSERT INTO ExamResults 
        (question_id,
        exam_name,
        student_answer_id,
        auto_question_points,
        auto_func_name_points,
        auto_func_name_comment,
        auto_case_1_points,
        auto_case_1_comment,
        auto_case_2_points,
        auto_case_2_comment,
        auto_case_3_points,
        auto_case_3_comment,
        edit_question_points,
        edit_func_name_points,
        edit_case_1_points,
        edit_case_2_points,
        edit_case_3_points) 
        VALUES
        ($current_question_id,
        '$exam_name',
        $current_student_answer_id,
        $current_question_response_autograde_points,
        $current_question_func_name_autograde_points,
        '$current_question_func_name_autograde_comment',
        $current_question_case_1_autograde_points,
        '$current_question_case_1_autograde_comment',
        $current_question_case_2_autograde_points,
        '$current_question_case_2_autograde_comment',
        $current_question_case_3_autograde_points,
        '$current_question_case_3_autograde_comment',
        $current_question_response_edit_points,
        $current_question_func_name_edit_points,
        $current_question_case_1_edit_points,
        $current_question_case_2_edit_points,
        $current_question_case_3_edit_points)";
    }

    else if(!empty($current_question_constraint_autograde_comment)) 
    {
        $sql_n="INSERT INTO ExamResults 
        (question_id,
        exam_name,
        student_answer_id,
        auto_question_points,
        auto_func_name_points,
        auto_func_name_comment,
        auto_constraint_points,
        auto_constraint_comment,
        auto_case_1_points,
        auto_case_1_comment,
        auto_case_2_points,
        auto_case_2_comment,
        edit_question_points,
        edit_func_name_points,
        edit_constraint_points,
        edit_case_1_points,
        edit_case_2_points) 
        VALUES
        ($current_question_id,
        '$exam_name',
        $current_student_answer_id,
        $current_question_response_autograde_points,
        $current_question_func_name_autograde_points,
        '$current_question_func_name_autograde_comment',
        $current_question_constraint_autograde_points,
        '$current_question_constraint_autograde_comment',
        $current_question_case_1_autograde_points,
        '$current_question_case_1_autograde_comment',
        $current_question_case_2_autograde_points,
        '$current_question_case_2_autograde_comment',
        $current_question_response_edit_points,
        $current_question_func_name_edit_points,
        $current_question_constraint_edit_points,
        $current_question_case_1_edit_points,
        $current_question_case_2_edit_points)";
    }
    else if(empty($current_question_constraint_autograde_comment)) 
    {
        $sql_n="INSERT INTO ExamResults 
        (question_id,
        exam_name,
        student_answer_id,
        auto_question_points,
        auto_func_name_points,
        auto_func_name_comment,
        auto_case_1_points,
        auto_case_1_comment,
        auto_case_2_points,
        auto_case_2_comment,
        edit_question_points,
        edit_func_name_points,
        edit_case_1_points,
        edit_case_2_points) 
        VALUES
        ($current_question_id,
        '$exam_name',
        $current_student_answer_id,
        $current_question_response_autograde_points,
        $current_question_func_name_autograde_points,
        '$current_question_func_name_autograde_comment',
        $current_question_case_1_autograde_points,
        '$current_question_case_1_autograde_comment',
        $current_question_case_2_autograde_points,
        '$current_question_case_2_autograde_comment',
        $current_question_response_edit_points,
        $current_question_func_name_edit_points,
        $current_question_case_1_edit_points,
        $current_question_case_2_edit_points)";
    }

    $query_n=mysqli_query($db,$sql_n) or die("there was a problem with the given data: " . mysqli_error($db));
}

$db->close(); // make sure to close it in case SQL server times out

?>