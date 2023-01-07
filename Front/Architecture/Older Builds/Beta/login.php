<?php
require_once(__DIR__ . "/db.php");
?>

<?php

$data = array(); // data to return to middle to return to front

$incoming_data = file_get_contents("php://input");

$decoded_data = json_decode($incoming_data, true);

$ucid="sn236";
$password="volleyball"; 

if(isset($decoded_data['ucid']))
    $ucid = $decoded_data['ucid'];

if(isset($decoded_data['password']))
    $password = $decoded_data['password'];

$sql_1="SELECT * FROM Users WHERE  `ucid`='$ucid'";
$query_1 = mysqli_query($db, $sql_1);
$result_1 = mysqli_fetch_row($query_1);

if($result_1)
{
    //echo "Data found<br>";

    $user_pw = $result_1[2]; // hash of user's password in database, if their username is valid
    $user_id = $result_1[0]; 

    if (password_verify($password, $user_pw))
    {
        $sql_2 = "SELECT role_id FROM UserRoles WHERE `user_id`='$user_id'";
        $query_2 = mysqli_query($db, $sql_2);
        $result_2 = mysqli_fetch_row($query_2);
        $user_role_id = $result_2[0];
        $sql_3 = "SELECT rolename FROM Roles WHERE `id`='$user_role_id'";
        $query_3 = mysqli_query($db, $sql_3);
        $result_3 = mysqli_fetch_row($query_3);
        $user_role = $result_3[0];
        //echo "user $ucid has role $user_role_id, $user_role<br>";
        //echo "$response<br> (password was correct)<br>";
        $data = array(
            'valid' => true,
            'user_role' => $user_role
            );
    }
    else
    {
    //echo "Password incorrect<br>";
    //echo "$response<br> (form was either not filled or data was not in database)<br>";
    //exit();
        $data = array(
        'valid' => false    
        );
    }

    
}
else 
{
    //echo "Data not found<br>";
    $data = array(
        'valid' => false    
        );
}

//$send_url = 'https://afsaccess4.njit.edu/~mjv43/CS490/Controller_Project/login_info.php'; // data to send to middle via JSON encoding

$payload = json_encode($data);

echo $payload;

$db->close(); // make sure to close it in case SQL server times out

?>

