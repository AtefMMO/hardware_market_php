<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
    $category = secureRequest($_GET['category']);
    $stmt = $connection->prepare("SELECT * FROM products WHERE category=?");
    $stmt->execute(array($category));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['status'] = "success";
    $response['categories'] = $rows;
}catch(PDOException $e){
    $response['status'] = "error";
    $response['message'] = "Error: ".$e->getMessage();
}
echo json_encode($response);