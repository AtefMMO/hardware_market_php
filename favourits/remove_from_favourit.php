<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
$user_id = secureRequest($_POST['user_id']);
$product_id = secureRequest($_POST['product_id']);
$stmt = $connection->prepare("DELETE FROM `favourits` WHERE `user_id` = ? AND `product_id` = ?");
$stmt->execute(array($user_id,$product_id));
$response['status'] = "success";
$response["message"] = "Product Removed From Favourits";
$response["product"] = [
    "user_id" => $user_id,
    "product_id" => $product_id
];
}catch(PDOException $e){
    $response['status'] = "error";
    $response["message"] = "Error: " . $e->getMessage();
}
echo json_encode($response);