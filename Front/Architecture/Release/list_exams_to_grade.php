<?php
require_once(__DIR__ . "/db.php");
?>

<?php

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

$sql_1="SELECT DISTINCT name FROM Exams";
$query_1 = mysqli_query($db, $sql_1);
while($names_from_db = mysqli_fetch_row($query_1))
{
    $names_of_exams[] = $names_from_db[0];
};

foreach($names_of_exams as $current_exam_name)
{
    // 1. check if there are responses to the current exam (StudentAnswers)
    // 2. if there are, make sure that the exam has not already been graded (i.e. in ExamResults), because an exam that has been graded should no longer be modified

    $sql_2="SELECT exam_name FROM StudentAnswers WHERE exam_name='$current_exam_name'"; 
    $query_2 = mysqli_query($db, $sql_2);

    if($query_2->num_rows > 0)
    {
        $sql_3="SELECT exam_name FROM ExamResults WHERE exam_name='$current_exam_name'";
        $query_3 = mysqli_query($db, $sql_3);
        if($query_3->num_rows == 0) // look for whether the current exam does NOT appear in ExamResults
        {
            $data[] = array(
                'exam_name' => $current_exam_name
            );
        }
    }
}

$payload = json_encode($data);

echo $payload;
//echo "Successfully inserted new question";

$db->close(); // make sure to close it in case SQL server times out

?>