<?php
include "../connect.php";
include "../core.php";
$response = [];
try {

    $product_id = secureRequest($_POST['product_id']);
    $user_id = secureRequest($_POST['user_id']);
    $stmt = $connection->prepare("INSERT INTO favourits (product_id,user_id) VALUES (?,?)");
    $stmt->execute([$product_id, $user_id]);
    $response['status'] = "success";
    $response['message'] = "Product added to favourit";
    $response['data'] = [
        "product_id" => $product_id,
        "user_id" => $user_id
    ];
} catch (Exception $e) {
    $response['status'] = "error";
    $response['message'] = "Invalid request";
}
echo json_encode($response);
