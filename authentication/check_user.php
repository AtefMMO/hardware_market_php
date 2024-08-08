<?php
include "../connect.php";
include "../core.php";
$response = [];
try {
    $token = secureRequest($_POST['token']);
    $user = checkToken($token);
    if ($user != false) {
        $response['status'] = "success";
        $response['message'] = "correct token";
        
    } else {
        $response['status'] = "error";
        $response['message'] = "Invalid token";
    }
} catch (PDOException $e) {
    $response['status'] = "error";
    $response['message'] = "Error: " . $e->getMessage();
}
