<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
    $user_id=secureRequest($_GET['user_id']);
    $stmt = $connection->prepare("SELECT * FROM `favourits` WHERE `user_id` = ?");
    $stmt->execute(array($user_id));
    $favourits = $stmt->fetchAll();
    for($i=0;$i<count($favourits);$i++){
        $favourits[$i]= getProductById($favourits[$i]['product_id']);//swap the product id with the product details
    }
    $response['status'] = "success";
    $response["message"] = "Favourits Retrieved Successfully";
    $response['favourits'] = $favourits;
    
    
}catch(PDOException $e){
    $response['status'] = "error";
    $response["message"] = "Error: " . $e->getMessage();
}
echo json_encode($response);