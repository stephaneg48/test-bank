<?php
require_once(__DIR__ . "/db.php");
?>

<?php

$data = array(); // data to return to middle to return to front... a list of exams

$incoming_data = file_get_contents("php://input");

$decoded_data = json_decode($incoming_data, true);

$exam_name_1 = "Exam 1";
$exam_name_2 = "Exam 2";

$test_data_1 = array(
    'examName' => $exam_name_1,
    'questionId' => 124,
    'points' => 50
);

$test_data_2 = array(
    'examName' => $exam_name_2,
    'questionId' => 135,
    'points' => 50
);

$test_data_3 = array(
    'examName' => $exam_name_1,
    'questionId' => 127,
    'points' => 50
);

$test_data_4 = array(
    'examName' => $exam_name_2,
    'questionId' => 140,
    'points' => 50
);

$exams = array(
    $test_data_1,
    $test_data_2,
    $test_data_3,
    $test_data_4
);

if(!empty($decoded_data)) // if it's empty, then it will use the test data above, which means something is wrong
    $exams = $decoded_data;

/*if(isset($decoded_data['user_role'])) // once it actually works, uncomment this
    $user_role = $decoded_data['user_role'];
else 
    {
        echo "You must be logged in and have permission to view this page.";
        exit();
    }
*/

print_r("<br>$exams<br>");
foreach($exams as $data)
    foreach($data as $item)
        echo "<br>$item<br>";

for ($i = 0; $i < count($exams); $i++) // inserting new questions into exam
{
    $new_question = $exams[$i];
    if ($new_question['points'] > 100)
        exit();
    $name = $new_question['examName'];
    $q_id = (int)$new_question['questionId'];
    $q_points = (int)$new_question['points'];
    
    $sql_1="INSERT INTO Exams (name,question_id,question_points) VALUES('$name',$q_id,$q_points)";
    
    $query_1 = mysqli_query($db, $sql_1) or die("there was a problem with the given data: " . mysqli_error($db));
}

$db->close(); // make sure to close it in case SQL server times out

?>