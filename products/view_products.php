<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
    $stmt = $connection->prepare("SELECT * FROM products");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['status'] = "success";
    $response['products'] = $rows;
}catch(PDOException $e){
    $response['status'] = "error";
    $response['message'] = "Error: ".$e->getMessage();
}
echo json_encode($response);