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

//echo "<br>decoded data echo: $decoded_data<br>";
//var_dump("<br>decoded data var dump: $decoded_data<br>");
//print_r("<br>decoded data print_r: $decoded_data<br>");

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

/*$exam = "Midterm";
$user_role = "Student";

if(isset($decoded_data['examName']))
    $exam = $decoded_data['examName'];

$sql_1="SELECT E.question_id, Q.id, Q.question, E.question_points
FROM Exams E, Questions Q
WHERE 1=1 AND E.name='$exam' AND E.question_id=Q.id";

$query_1 = mysqli_query($db, $sql_1);
if($query_1->num_rows > 0)
{
    while($question_in_exam = mysqli_fetch_assoc($query_1)) // look at each question
    {
        $current_id = $question_in_exam['question_id']; // get the current question's id

        // get what question says
        $sql_2="SELECT question FROM Questions WHERE id=$current_id";
        $query_2 = mysqli_query($db, $sql_2);
        $result = mysqli_fetch_row($query_2); // should just be one result, otherwise that's a problem
        // end get what question says

        $current_question_points = $question_in_exam['question_points']; // get the current question's points

        $current_exam_questions[] = $result[0]; // store what current question says into array
        $current_exam_questions_pt_values[] = $current_question_points; // store current question's point value into array
        $current_exam_questions_ids[] = $current_id; // store current question's id into array
    }
}
else
{
    echo "This exam is empty and is somehow able to be taken...";
}

$data[] = array(
    'examName' => $exam,
    'questions' => $current_exam_questions,
    'points' => $current_exam_questions_pt_values,
    'question_ids' => $current_exam_questions_ids
);

$payload = json_encode($data);

echo $payload;*/

$db->close(); // make sure to close it in case SQL server times out

?>