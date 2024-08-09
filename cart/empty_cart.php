<?php
include "../connect.php";
include "../core.php";
$response = [];
try {
    $user_id = secureRequest($_GET['user_id']);
    deleteAllProductsInCart($user_id);
    $response['status'] = "success";
    $response['message'] = "Cart is empty";
} catch (PDOException $e) {
    $response["status"] = "error";
    $response["message"] = "Invalid request";
}
echo json_encode($response);
