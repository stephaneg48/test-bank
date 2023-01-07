<?php
require_once(__DIR__ . "/db.php");
?>

<?php

// this file is to list the exams that are available for a student to take

$data = array(); // data to return to middle to return to front... a list of exams

/*if(isset($decoded_data['user_role'])) // once it actually works, uncomment this
    $user_role = $decoded_data['user_role'];
else 
    {
        echo "You must be logged in and have permission to view this page.";
        exit();
    }
*/

// get every exam to prepare to return...

// first... get the name of each exam
$names_of_exams = [];

$sql_1="SELECT name FROM Exams";
$query_1 = mysqli_query($db, $sql_1);
while($names_from_db = mysqli_fetch_row($query_1))
{
    $names_of_exams[] = $names_from_db[0];
};
// now make sure to only look at the unique names...
$names_of_exams = array_unique($names_of_exams);

foreach($names_of_exams as $current_exam_name)
{
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

$db->close(); // make sure to close it in case SQL server times out

?>