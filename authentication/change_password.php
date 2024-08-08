<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
    $id=secureRequest($_POST['id']);
    $email=secureRequest($_POST['email']);
    $old_password=secureRequest($_POST['old_password']);
    $new_password=secureRequest($_POST['new_password']);
   
   
    $check=checkOldPassword($id, $old_password);
    if($check==false){
        $response['status'] = 'error';
        $response['message'] = 'Invalid password';
        echo json_encode($response);
        return;
    }
    if($old_password==$new_password){
        $response['status'] = 'error';
        $response['message'] = 'New password cannot be the same as the old password';
        echo json_encode($response);
        return;
    }
    $new_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $connection->prepare("UPDATE `users` SET `password` = ? WHERE `id` = ?");
    $stmt->execute(array($new_password, $id));
    $response['status'] = "success";
    $response["message"] = "Password changed successfully";
    $response["user"] = [
        "e-mail"=>$email,
        "id" => $id, 
    ];
}catch(PDOException $e){
    $response['status'] = "error";
    $response['message'] = "Error: ".$e->getMessage();
}
echo json_encode($response);