<?php

use function PHPSTORM_META\map;

require_once(__DIR__ . "/db.php");
?>

<?php

// goal: send data to front in the exact same format that it was sent to ME in insert_scores.php

$data = array();

$incoming_data = file_get_contents("php://input");

$decoded_data = json_decode($incoming_data, true);

$exam_name = "";

$graded_exam = array(
    'exam_name' => "NewTest"
);

if(!empty($decoded_data)) // if it's empty, then it will use the test data above, which means something is wrong
    $graded_exam = $decoded_data;

// need to know whether the student's answer shows in the results

// DATA TO POPULATE & RETURN
$exam_name = $graded_exam['exam_name'];
$autograde_info = array();
$edited_info = array();
$final_points = 0;
// END DATA TO POPULATE & RETURN


// 1. get EVERYTHING in ExamResults for that exam

$q_ids = array(); // done, goes in 'autograde'
$answer_ids = array(); // just for getting the teacher's comments
$questions = array(); // done, goes in 'autograde'
$auto_total_per_question = array(); // done, goes in 'autograde'
$auto_points_for_func_name_per_question = array(); // just the values... each value is stored in an array, with a string pointing to it
$auto_comment_for_func_name_per_question = array();
$auto_points_for_constraint_per_question = array();
$auto_comment_for_constraint_per_question = array();
$auto_points_for_case_1_per_question = array(); // just the values... each value is stored in an array, with a string pointing to it
$auto_comment_for_case_1_per_question = array();
$auto_points_for_case_2_per_question = array(); // just the values... each value is stored in an array, with a string pointing to it
$auto_comment_for_case_2_per_question = array();
$auto_points_for_case_3_per_question = array(); // just the values... each value is stored in an array, with a string pointing to it
$auto_comment_for_case_3_per_question = array();
$auto_points_for_case_4_per_question = array(); // just the values... each value is stored in an array, with a string pointing to it
$auto_comment_for_case_4_per_question = array();
$auto_points_for_case_5_per_question = array(); // just the values... each value is stored in an array, with a string pointing to it
$auto_comment_for_case_5_per_question = array();

$edit_total_per_question = array();
$edit_points_for_func_name_per_question = array();
$edit_points_for_constraint_per_question = array();
$edit_points_for_case_1_per_question = array();
$edit_points_for_case_2_per_question = array();
$edit_points_for_case_3_per_question = array();
$edit_points_for_case_4_per_question = array();
$edit_points_for_case_5_per_question = array();
$edit_comments_per_question = array();

$sql_1="SELECT * FROM ExamResults where exam_name = '$exam_name'";
$query_1 = mysqli_query($db, $sql_1);

while($result = mysqli_fetch_row($query_1))
{
    $q_ids[] = (int)$result[1];
    $answer_ids[] = (int)$result[3];
    $auto_total_per_question[] = (float)$result[4];
    $auto_points_for_func_name_per_question[] = (float)$result[5];
    $auto_comment_for_func_name_per_question[] = $result[6];
    if(!empty($result[8])) // again, look for the comment...
    {
        $auto_points_for_constraint_per_question[] = $result[7];
        $auto_comment_for_constraint_per_question[] = $result[8];
    }
    else
    {
        $auto_points_for_constraint_per_question[] = 0;
        $auto_comment_for_constraint_per_question[] = "";
    }
    $auto_points_for_case_1_per_question[] = (float)$result[9];
    $auto_comment_for_case_1_per_question[] = $result[10];
    $auto_points_for_case_2_per_question[] = (float)$result[11];
    $auto_comment_for_case_2_per_question[] = $result[12];
    if(!empty($result[14]))
    {
        $auto_points_for_case_3_per_question[] = (float)$result[13];
        $auto_comment_for_case_3_per_question[] = $result[14];
    }
    if(!empty($result[16]))
    {
        $auto_points_for_case_4_per_question[] = (float)$result[15];
        $auto_comment_for_case_4_per_question[] = $result[16];
    }
    if(!empty($result[18]))
    {
        $auto_points_for_case_5_per_question[] = (float)$result[17];
        $auto_comment_for_case_5_per_question[] = $result[18];
    }
    
    $edit_total_per_question[] = (float)$result[19];
    $edit_points_for_func_name_per_question[] = (float)$result[20];
    if(!empty($result[21]))
        $edit_points_for_constraint_per_question[] = (float)$result[21];
    $edit_points_for_case_1_per_question[] = (float)$result[22];
    $edit_points_for_case_2_per_question[] = (float)$result[23];
    if(!empty($result[24]))
        $edit_points_for_case_3_per_question[] = (float)$result[24];
    if(!empty($result[25]))
        $edit_points_for_case_4_per_question[] = (float)$result[25];
    if(!empty($result[26]))
        $edit_points_for_case_5_per_question[] = (float)$result[26];

};

$name_check = array(); // for 'autograde'
$constraint_check = array(); // for 'autograde'
$test_case_r = array(); // for 'autograde'
$edit_results = array();

for ($i = 0; $i < count($q_ids); $i++) // just using q_ids because everything else uses the same count...
{
    $current_question = array(); // make a new array for each question for the test case stuff

    $name_check[] = [
        'comment' => $auto_comment_for_func_name_per_question[$i],
        'points_received' => $auto_points_for_func_name_per_question[$i]
    ];
    
    if(empty($auto_comment_for_constraint_per_question[$i])) // again, check the comments...
    {
        $constraint_check[] = [];
    }
    else
    {
        $constraint_check[] = [
            'comment' => $auto_comment_for_constraint_per_question[$i],
            'points_received' => $auto_points_for_constraint_per_question[$i]
        ];
    }

    if(!empty($auto_comment_for_case_5_per_question[$i])) // again, check the comments...
    {
        $test_case_r[] = [
            $current_question[$i] = [
                'case_1' => $auto_points_for_case_1_per_question[$i],
                'comments' => $auto_comment_for_case_1_per_question[$i]
            ],
            $current_question[$i+1] = [
                'case_2' => $auto_points_for_case_2_per_question[$i],
                'comments' => $auto_comment_for_case_2_per_question[$i]
            ],
            $current_question[$i+2] = [
                'case_3' => $auto_points_for_case_3_per_question[$i],
                'comments' => $auto_comment_for_case_3_per_question[$i]
            ],
            $current_question[$i+3] = [
                'case_4' => $auto_points_for_case_4_per_question[$i],
                'comments' => $auto_comment_for_case_4_per_question[$i]
            ],
            $current_question[$i+4] = [
                'case_5' => $auto_points_for_case_5_per_question[$i],
                'comments' => $auto_comment_for_case_5_per_question[$i]
            ]
        ];
    }
    else if (!empty($auto_comment_for_case_4_per_question[$i]))
    {
        $test_case_r[] = [
            $current_question[$i] = [
                'case_1' => $auto_points_for_case_1_per_question[$i],
                'comments' => $auto_comment_for_case_1_per_question[$i]
            ],
            $current_question[$i+1] = [
                'case_2' => $auto_points_for_case_2_per_question[$i],
                'comments' => $auto_comment_for_case_2_per_question[$i]
            ],
            $current_question[$i+2] = [
                'case_3' => $auto_points_for_case_3_per_question[$i],
                'comments' => $auto_comment_for_case_3_per_question[$i]
            ],
            $current_question[$i+3] = [
                'case_4' => $auto_points_for_case_4_per_question[$i],
                'comments' => $auto_comment_for_case_4_per_question[$i]
            ],
        ];
    }
    else if (!empty($auto_comment_for_case_3_per_question[$i]))
    {
        $test_case_r[] = [
            $current_question[$i] = [
                'case_1' => $auto_points_for_case_1_per_question[$i],
                'comments' => $auto_comment_for_case_1_per_question[$i]
            ],
            $current_question[$i+1] = [
                'case_2' => $auto_points_for_case_2_per_question[$i],
                'comments' => $auto_comment_for_case_2_per_question[$i]
            ],
            $current_question[$i+2] = [
                'case_3' => $auto_points_for_case_3_per_question[$i],
                'comments' => $auto_comment_for_case_3_per_question[$i]
            ],
        ];
    }
    else
    {
        $test_case_r[] = [
            $current_question[$i] = [
                'case_1' => $auto_points_for_case_1_per_question[$i],
                'comments' => $auto_comment_for_case_1_per_question[$i]
            ],
            $current_question[$i+1] = [
                'case_2' => $auto_points_for_case_2_per_question[$i],
                'comments' => $auto_comment_for_case_2_per_question[$i]
            ],
        ];
    }

    $current_question_id = $q_ids[$i];
    $current_response_id = $answer_ids[$i];
    $final_points += $edit_total_per_question[$i];

    $sql_2="SELECT comments FROM StudentAnswers WHERE id=$current_response_id AND exam_name='$exam_name' AND question_id=$current_question_id";
    $query_2 = mysqli_query($db, $sql_2);
    $result = mysqli_fetch_row($query_2);
    $edit_comments_per_question[] = $result[0];
}

// get the question contents and the points value for each question

$question_points_values = array();

foreach($q_ids as $q_id)
{
    $sql_3="SELECT question FROM Questions WHERE id=$q_id";
    $query_3 = mysqli_query($db, $sql_3);
    $result = mysqli_fetch_row($query_3);
    $questions[] = $result[0];

    $sql_4="SELECT question_points FROM Exams WHERE name='$exam_name' AND question_id=$q_id";
    $query_4=mysqli_query($db,$sql_4);
    while($res = mysqli_fetch_row($query_4))
    {
        $question_points_values[] = (int)$res[0];
    }
}

$autograde_info = array(
    'constraint_check' => $constraint_check,
    'name_check' => $name_check,
    'question_ids' => $q_ids,
    'questions' => $questions,
    'test_case_r' => $test_case_r,
    'total_perQ' => $auto_total_per_question
);

$edited_info = array(
    'nameEdit' => $edit_points_for_func_name_per_question,
    'test1_edit' => $edit_points_for_case_1_per_question,
    'test2_edit' => $edit_points_for_case_2_per_question,
    'test3_edit' => $edit_points_for_case_3_per_question,
    'test4_edit' => $edit_points_for_case_4_per_question,
    'test5_edit' => $edit_points_for_case_5_per_question,
    'constraintEdit' => $edit_points_for_constraint_per_question,
    'total_perQ_edit' => $edit_total_per_question,
    'comments_edit' => $edit_comments_per_question
);

$data = array(
    'autograde' => $autograde_info,
    'edit_results' => $edited_info,
    'examName' => $exam_name, 
    'examTotal' => round($final_points, 2),
    'points_perQ' => $question_points_values
);

$payload = json_encode($data);

echo $payload;

$db->close(); // make sure to close it in case SQL server times out

?>