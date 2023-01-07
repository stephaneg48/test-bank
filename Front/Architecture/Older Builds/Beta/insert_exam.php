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


/*
// now, get every exam to prepare to return...

// first... get the name of each exam

$names_of_exams = [];
for($j = 0; $j < count($exams); $j++)
{
    $current_exam = $exams[$j];
    $current_exam_name = $current_exam['examName'];
    $names_of_exams[] = $current_exam_name;
}

// now make sure to only look at the unique names...
$names_of_exams = array_unique($names_of_exams);

for ($k = 0; $k < count($names_of_exams); $k++)
{
    $current_exam_name = $names_of_exams[$k];

    $sql_2="SELECT name, question_id, question_points FROM Exams WHERE name='$current_exam_name'"; // get all the exams
    $query_2 = mysqli_query($db, $sql_2);

    $current_exam_questions = array();
    $current_exam_questions_pt_values = array();
    $current_exam_questions_ids = array();

    if($query_2->num_rows > 0)
    {
        $current_name = "";
        while($question_in_exam = mysqli_fetch_assoc($query_2)) // look at each question
        {
            $current_id = $question_in_exam['question_id']; // get the current question's id

            // get what question says
            $sql_3="SELECT question FROM Questions WHERE id=$current_id";
            $query_3 = mysqli_query($db, $sql_3);
            $result = mysqli_fetch_row($query_3); // should just be one result, otherwise that's a problem
            // end get what question says

            $current_question_points = $question_in_exam['question_points']; // get the current question's points

            $current_exam_questions[] = $result[0]; // store what current question says into array
            $current_exam_questions_pt_values[] = $current_question_points; // store current question's point value into array
            $current_exam_questions_ids[] = $current_id; // store current question's id into array
        }
    }

    $data[] = array( // should be multiple exams
        'examName' => $current_exam_name,
        'questions' => $current_exam_questions,
        'points' => $current_exam_questions_pt_values,
        'question_ids' => $current_exam_questions_ids
    );
}

$payload = json_encode($data);

echo $payload;
//echo "Successfully inserted new question";
*/
$db->close(); // make sure to close it in case SQL server times out

?>