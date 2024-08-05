<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
$user_id = secureRequest($_POST['user_id']);
$product_id = secureRequest($_POST['product_id']);
$stmt = $connection->prepare("INSERT INTO cart (product_id,user_id) VALUES (?,?)");
$stmt->execute(array($product_id,$user_id));
$product=getProductById($product_id);
$response['status']="success";  
$response['message']="Product added to cart";
}catch(PDOException $e){
    $response["status"]="error";
    $response["message"]="Invalid request";
}
echo json_encode($response);