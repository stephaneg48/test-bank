<?php
require_once(__DIR__ . "/db.php");
?>

<?php

$data = array();

/*
$student_exam_results = array( // TEST DATA
    'exam_name' => "Midterm",
    'question_id' => [333, 336, 340],
    'auto_result' => array(
        'name_check' => [10, 0, 0],
        'testcase1_check' => [10, 0, 0],
        'testcase2_check' => [10, 0, 0]
    ),
    'edit_result' => array(
        'name_check' => [10, 0, 0],
        'testcase1_check' => [10, 0, 0],
        'testcase2_check' => [10, 0, 0]
    ),
    'total_received' => 30
);
*/

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

// should only need the name of the exam to show the questions?

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

$questions = array();
$responses = array();
$responses_ids = array();
$question_points = array();


$q_ids = $autograde_result['question_ids']; // array
$name_check_points = $edited_result['nameEdit']; // array of points earned for name check for each question
$testcase1_check_points = $edited_result['test1_edit']; // array of points earned for test case 1 check for each question
$testcase2_check_points = $edited_result['test2_edit']; // array of points earned for test case 2 check for each question
//$testcase3_check_points = $edited_result[3]; // array of points earned for test case 3 check for each question
//$testcase4_check_points = $edited_result[4]; // array of points earned for test case 4 check for each question
//$testcase5_check_points = $edited_result[5]; // array of points earned for test case 5 check for each question
$comments = $edited_result['comments_edit'];

for($i = 0; $i < count($name_check_points); $i++) 
// just using name_check_points at random here, it could have been the other two since they're always the same size
{
    $temp = 0;
    $temp += $name_check_points[$i] + $testcase1_check_points[$i] + $testcase2_check_points[$i];
    $question_points[$i] = $temp;
}

// now... 


// 3. already have autograde_answer_points
// 4. already have edited_answer_points
// 5. insert into ExamResults
// 5.1. use edited_answer_points to store question_points (sum of one question's points accumulated) and the individual check's points

foreach($q_ids as $q_id) 
{
    $sql_1="SELECT question FROM Questions WHERE id=$q_id"; // 1. use the question ids to get the questions themselves
    $query_1=mysqli_query($db,$sql_1);
    while($question = mysqli_fetch_assoc($query_1))
    {
        $questions[] = $question['question'];
    };

    // 2. use the question ids and the exam name to get the student's responses to each question AND the id of the student response
    $sql_2="SELECT id,student_answer,comments FROM StudentAnswers WHERE exam_name='$exam_name' AND question_id=$q_id"; 
    $query_2=mysqli_query($db,$sql_2);
    while($result = mysqli_fetch_assoc($query_2))
    {
        $responses[] = $result['student_answer'];
        $responses_ids[] = $result['id'];
    }
}

//$final_points = array_sum($edited_result['name_check']) + array_sum($edited_result['testcase1_check']) + array_sum($edited_result['testcase2_check']);

for($i = 0; $i < count($questions); $i++) // for 1 exam, N question ids, N answer ids, N responses, ...
{
    $current_question_id = $q_ids[$i];
    // exam name...
    $current_student_answer_id = $responses_ids[$i];
    $current_question_response_points = $question_points[$i];
    $current_question_func_name_points = $name_check_points[$i];
    $current_question_case_1_points = $testcase1_check_points[$i];
    $current_question_case_2_points = $testcase2_check_points[$i];

    $sql_n="INSERT INTO ExamResults 
    (question_id,
    exam_name,
    student_answer_id,
    question_points,
    func_name_points,
    case_1_points,
    case_2_points) 
    VALUES
    ($current_question_id,
    '$exam_name',
    $current_student_answer_id,
    $current_question_response_points,
    $current_question_func_name_points,
    $current_question_case_1_points,
    $current_question_case_2_points)";
    
    $query_n=mysqli_query($db,$sql_n) or die("there was a problem with the given data: " . mysqli_error($db));
}

$data = array(
    'exam_name' => $exam_name,
    'questions' => $questions,
    'answers' => $responses,
    'autograde_answer_points' => $autograde_result,
    'edited_answer_points' => $edited_result,
    'total_points' => $final_points,
    'comments' => $comments
);

$payload = json_encode($data);

echo $payload;
//echo "Successfully inserted new question";

$db->close(); // make sure to close it in case SQL server times out

?>