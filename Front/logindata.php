<?php //print_r($_POST); 
session_start();
$send_url = "/../Middle/login_info.php";

    $ch = curl_init($send_url);
    $data = array(
                'ucid' => $_POST['ucid'],
                'password' =>$_POST['password']
            );
                
    $payload = json_encode($data);
    curl_setopt($ch, CURLOPT_URL, $send_url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

   

    //echo $result;

    $a = explode("}", $result);
    $value = $a[0];
    $value = "$value}";

    $response = json_decode($value, true);
    error_log("response was: " . var_export($response, true));
    if(isset($response['user_role'])){
        $role = $response['user_role'];
        if($role == 'Student'){
            die(header("Location: student_landing.php"));
        }
        elseif($role == 'Teacher'){
            die(header("Location: teacher_landing.php"));
        }
    }
   else{
       $_SESSION['errorMessage'] = true;
       header("Location: index.php");
       exit();
   }
   
 ?>


