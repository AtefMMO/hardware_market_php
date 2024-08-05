<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
$user_id = secureRequest($_GET['user_id']);
$stmt = $connection->prepare("SELECT * FROM cart WHERE user_id=?");
$stmt->execute([$user_id]);
$cart = $stmt->fetchAll();
$response['status']="success";
$response['message']="Cart items retrieved";
for($i=0;$i<count($cart);$i++){
    $product=getProductById($cart[$i]['product_id']);
    $cart[$i]=$product;
}
$response['products']=$cart;
}catch(PDOException $e){
 $response["status"]="error";
    $response["message"]="Invalid request";
}
echo json_encode($response);