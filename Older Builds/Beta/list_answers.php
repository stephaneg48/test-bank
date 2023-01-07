<?php
require_once(__DIR__ . "/db.php");
?>

<?php

$data = array(); // data to return to middle to return to front... a list of exams

$incoming_data = file_get_contents("php://input");

$decoded_data = json_decode($incoming_data, true);

$exam = array(
    'exam_name' => "Midterm"
);
$exam_name = "";

if(!empty($decoded_data)) // if it's empty, then it will use the test data above, which means something is wrong
    $exam = $decoded_data;

/*if(isset($decoded_data['user_role'])) // once it actually works, uncomment this
    $user_role = $decoded_data['user_role'];
else 
    {
        echo "You must be logged in and have permission to view this page.";
        exit();
    }
*/

$exam_name = $exam['exam_name']; // only receiving one, but it does not matter

// get every question to prepare to return...

// first... get the question ids for the questions of the exam
$q_ids = [];
$q_points = [];

$sql_1="SELECT question_id, question_points FROM Exams WHERE name='$exam_name'";
$query_1 = mysqli_query($db, $sql_1);
while($result = mysqli_fetch_row($query_1))
{
    $q_ids[] = $result[0];
    $q_points[] = $result[1];
};

foreach(array_combine($q_ids, $q_points) as $current_id => $current_points)
{
    // get what question says
    $sql_2="SELECT question FROM Questions WHERE id=$current_id";
    $query_2 = mysqli_query($db, $sql_2);
    $result = mysqli_fetch_row($query_2); // should just be one result, otherwise that's a problem
    // end get what question says

    $current_question = $result[0]; // store what current question says into variable

    $sql_3="SELECT function_name, case_1, case_2 FROM TestCases WHERE question_id='$current_id'"; 
    // selects one question's function name, test case 1 and test case 2... this should ignore the weird issue with blanks in the database
    $query_3 = mysqli_query($db, $sql_3);

    while($question_data = mysqli_fetch_assoc($query_3))
    {
        $question_func = $question_data['function_name'];
        $current_question_case_1 = $question_data['case_1'];
        $current_question_case_2 = $question_data['case_2'];
    }

    $sql_4 = "SELECT student_answer FROM StudentAnswers WHERE question_id=$current_id AND exam_name='$exam_name'";
    $query_4 = mysqli_query($db, $sql_4);

    $student_response = mysqli_fetch_row($query_4);
    $response_text = $student_response[0];

    $data[] = array(
        'question_id' => $current_id,
        'question_points' => $current_points,
        'student_answer' => $response_text,
        'question' => $current_question,
        'function' => $question_func,
        'case_1' => $current_question_case_1,
        'case_2' => $current_question_case_2
    );
}

$payload = json_encode($data);

echo $payload;
//echo "Successfully inserted new question";

$db->close(); // make sure to close it in case SQL server times out

?>