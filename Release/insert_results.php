<?php
require_once(__DIR__ . "/db.php");
?>

<?php

$data = array(); // data to return to middle to return to front... a list of exams

$responses = array();

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
    $responses = $decoded_data;

foreach($responses as $new_response) // inserting new questions into exam
{
    $student_answer = $new_response['answer'];
    $q_id = (int)$new_response['question_id'];
    $exam_name = $new_response['exam_name'];

    $sql_1="INSERT INTO StudentAnswers (question_id,exam_name,student_answer) VALUES($q_id,'$exam_name','$student_answer')";
    
    $query_1 = mysqli_query($db, $sql_1) or die("there was a problem with the given data: " . mysqli_error($db));
}

$db->close(); // make sure to close it in case SQL server times out

?>