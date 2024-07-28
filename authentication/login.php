<?php
include "../connect.php";
include "../core.php";
$response = [];
try {

    $email = secureRequest($_POST['e-mail']);
    $password = secureRequest($_POST['password']);
    if (empty($email) || empty($password)) {
        $response['status'] = 'error';
        $response['message'] = 'Please fill all fields';
        echo json_encode($response);
        return;
    }
    $user=checkLoginCredentials($email, $password);
    if (!$user) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid email or password';
        echo json_encode($response);
        return;
    }

    $response['status'] = "success";
    $response["message"] = "logged in successfully";
  $response["user"]=[
        "name"=>$user['name'],
        "e-mail"=>$user['e-mail'],
        "address"=>$user['address'],
        "token"=>$user['token']
    ];
} catch (PDOException $e) {
    $response['status'] = "error";
    $response["message"] = "Error: " . $e->getMessage();
}
echo json_encode($response);
