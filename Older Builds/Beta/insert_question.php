<?php
require_once(__DIR__ . "/db.php");
?>

<?php

$data = array(); // data to return to middle to return to front

$incoming_data = file_get_contents("php://input");

$decoded_data = json_decode($incoming_data, true);

$user_role="Teacher";
$question="";
$difficulty="";
$function="";
$case_1="";
$case_2="";
$category="";

/*if(isset($decoded_data['user_role'])) // once it actually works, uncomment this
    $user_role = $decoded_data['user_role'];
else 
    {
        echo "You must be logged in and have permission to view this page.";
        exit();
    }
*/

if(isset($decoded_data['questionDescription'])) // $question
    $question = $decoded_data['questionDescription'];

if(isset($decoded_data['difficulty'])) 
    $difficulty = $decoded_data['difficulty'];

if(isset($decoded_data['questionFunction'])) // $function
    $function = $decoded_data['questionFunction'];

if(isset($decoded_data['testcase1'])) // $case_1
    $case_1 = $decoded_data['testcase1'];

if(isset($decoded_data['testcase2'])) // $case_2
    $case_2 = $decoded_data['testcase2'];

if(isset($decoded_data['questionCateogory'])) // $category
    $category = $decoded_data['questionCateogory'];

// Loop, Conditional, Recursion, List, Strings, Arithmetic, Others

$sql_1="INSERT INTO Questions (question,difficulty,topic) VALUES('$question', '$difficulty','$category')"; // insert the question
$query_1 = mysqli_query($db, $sql_1) or die("there was a problem with the given data: " . mysqli_error($db));

$sql_for_id="SELECT id FROM Questions WHERE question='$question'"; // get the ID of the question that was just inserted
$query_for_id=mysqli_query($db,$sql_for_id);
$id_result = mysqli_fetch_row($query_for_id);

$inserted_question_id = 0;

foreach($id_result as $detail) // simplest thing I could think of...
    $inserted_question_id = $detail;

//echo "<br>the newly inserted question's id: $inserted_question_id<br>";

$sql_2="SELECT id, question, difficulty, topic FROM Questions"; // just to show what questions exist, if any...
$query_2 = mysqli_query($db, $sql_2);

if($query_2->num_rows > 0)
{
    while($question_in_bank = mysqli_fetch_assoc($query_2))
    {
        $data[] = array(
            'id' => $question_in_bank['id'],
            'question' => $question_in_bank['question'],
            'difficulty' => $question_in_bank['difficulty'],
            'category' => $question_in_bank['topic']
        );    
    }
         
}

// insert the test cases for the question that was just put into the question bank
$sql_3="INSERT INTO TestCases (question_id, function_name, case_1, case_2) VALUES('$inserted_question_id', '$function', '$case_1', '$case_2')";
$query_3 = mysqli_query($db, $sql_3) or die("there was a problem with the given data: " . mysqli_error($db));


//echo "<br>decoded data: $decoded_data<br>";









$payload = json_encode($data);

echo $payload;
//echo "Successfully inserted new question";

$db->close(); // make sure to close it in case SQL server times out

?>