<?php
require_once(__DIR__ . "/db.php");
?>

<?php

// when student clicks the "view grades" page button, this page should send back a list of graded exams
// this is mostly the exact opposite of list_exams_to_grade.php...

$data = array();

// get the name of each exam
$names_of_exams = [];

$sql_1="SELECT DISTINCT exam_name FROM ExamResults";
$query_1 = mysqli_query($db, $sql_1);
while($names_from_db = mysqli_fetch_row($query_1))
{
    $names_of_exams[] = $names_from_db[0];
};

foreach($names_of_exams as $current_exam_name)
{
    $data[] = array(
        'exam_name' => $current_exam_name
    );
}

if (empty($data))
{
    echo "There are no graded exams yet...";
}
else
{
    $payload = json_encode($data);
    echo $payload;
    
    $db->close(); // make sure to close it in case SQL server times out
}

?>