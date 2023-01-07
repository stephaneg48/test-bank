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
$case_3="";
$case_4="";
$case_5="";
$category="";
$constraint="";
$request="";
$search_category="";
$search_difficulty="";
$search_keyword="";

/*if(isset($decoded_data['user_role'])) // once it actually works, uncomment this
    $user_role = $decoded_data['user_role'];
else 
    {
        echo "You must be logged in and have permission to view this page.";
        exit();
    }
*/

// stuff for returning
if(isset($decoded_data['request'])) 
    $request = $decoded_data['request'];
// if request is false, insert and return entire test bank
// if request is true, only return from the test bank what's being specified from filter

if(isset($decoded_data['category'])) 
    $search_category = $decoded_data['category'];
if(isset($decoded_data['difficulty'])) 
    $search_difficulty = $decoded_data['difficulty'];
if(isset($decoded_data['key'])) 
    $search_keyword = $decoded_data['key'];
// end stuff for returning

// stuff for inserting
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

if(isset($decoded_data['testcase3'])) // $case_3
    $case_3 = $decoded_data['testcase3'];

if(isset($decoded_data['testcase4'])) // $case_4
    $case_4 = $decoded_data['testcase4'];

if(isset($decoded_data['testcase5'])) // $case_5
    $case_5 = $decoded_data['testcase5'];

if(isset($decoded_data['questionCateogory'])) // $category
    $category = $decoded_data['questionCateogory'];

if(isset($decoded_data['QuestionConstraint'])) // $constraint
    $constraint = $decoded_data['QuestionConstraint'];
// end stuff for inserting

// Loop, Conditional, Recursion, List, Strings, Arithmetic

if(empty($request)) // if no search is made, just show the test bank
{
    $sql_1="SELECT id, question, difficulty, topic FROM Questions"; // just to show what questions exist, if any... this should be modified if filter is set, though
    $query_1 = mysqli_query($db, $sql_1);

    if($query_1->num_rows > 0)
    {
        while($question_in_bank = mysqli_fetch_assoc($query_1))
        {
            $data[] = array(
                'id' => $question_in_bank['id'],
                'question' => $question_in_bank['question'],
                'difficulty' => $question_in_bank['difficulty'],
                'category' => $question_in_bank['topic']
            );    
        }
    }

    $payload = json_encode($data);

    echo $payload;
    //echo "Successfully inserted new question";

    $db->close(); // make sure to close it in case SQL server times out
}

else if($request == 'false') // insert and return entire test bank
{
    $sql_1="INSERT INTO Questions (question,difficulty,topic,q_constraint) VALUES('$question', '$difficulty','$category', '$constraint')"; // insert the question
    $query_1 = mysqli_query($db, $sql_1) or die("there was a problem with the given data: " . mysqli_error($db));

    $sql_for_id="SELECT id FROM Questions WHERE question='$question'"; // get the ID of the question that was just inserted
    $query_for_id=mysqli_query($db,$sql_for_id);
    $id_result = mysqli_fetch_row($query_for_id);

    $inserted_question_id = 0;

    foreach($id_result as $detail) // simplest thing I could think of...
        $inserted_question_id = $detail;

    //echo "<br>the newly inserted question's id: $inserted_question_id<br>";

    // insert the test cases for the question that was just put into the question bank
    $sql_2="INSERT INTO TestCases (question_id, function_name, case_1, case_2, case_3, case_4, case_5) VALUES('$inserted_question_id', '$function', '$case_1', '$case_2', '$case_3', '$case_4', '$case_5')";
    $query_2 = mysqli_query($db, $sql_2) or die("there was a problem with the given data: " . mysqli_error($db));

    $sql_3="SELECT id, question, difficulty, topic FROM Questions"; // just to show what questions exist, if any... this should be modified if filter is set, though
    $query_3 = mysqli_query($db, $sql_3);

    if($query_3->num_rows > 0)
    {
        $questions_in_bank = mysqli_fetch_all($query_3);
        foreach($questions_in_bank as $question_in_bank)
        {
            $data[] = array(
                'id' => $question_in_bank['id'],
                'question' => $question_in_bank['question'],
                'difficulty' => $question_in_bank['difficulty'],
                'category' => $question_in_bank['topic']
            );    
        }
    }

    $payload = json_encode($data);

    echo $payload;
    //echo "Successfully inserted new question";

    $db->close(); // make sure to close it in case SQL server times out
}
else if ($request == 'true') // only return from the test bank what's being specified from filter
{
    // if keyword is included...
    if(!empty($search_keyword))
    {
        $sql_1="SELECT id, question, difficulty, topic FROM Questions WHERE topic='$search_category' AND difficulty='$search_difficulty' AND question LIKE '%$search_keyword%'";
        $query_1 = mysqli_query($db, $sql_1);
    }

    // else if keyword is not included...
    else
    {
        $sql_1="SELECT id, question, difficulty, topic FROM Questions WHERE topic='$search_category' AND difficulty='$search_difficulty'";
        $query_1 = mysqli_query($db, $sql_1);
    }
    
    if($query_1->num_rows > 0)
    {
        while($question_in_bank = mysqli_fetch_assoc($query_1))
        {
            $data[] = array(
                'id' => $question_in_bank['id'],
                'question' => $question_in_bank['question'],
                'difficulty' => $question_in_bank['difficulty'],
                'category' => $question_in_bank['topic']
            );    
        }
    }

    $payload = json_encode($data);

    echo $payload;
    //echo "Successfully inserted new question";

    $db->close(); // make sure to close it in case SQL server times out
}

?>