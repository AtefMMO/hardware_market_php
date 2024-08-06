<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
$stmt = $connection->prepare("SELECT * FROM `orders` WHERE `order_status`='pending'");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response['status'] = "success";
$response['message'] = "Orders retrieved successfully";
$response['orders'] = $rows;

}catch(PDOException $e){
    $response['status'] = "error";
    $response['message'] = "Error: ".$e->getMessage();
}
echo json_encode($response);