<?php
include "../connect.php";
include "../core.php";
$response = [];
try {
    $stmt = $connection->prepare("SELECT DISTINCT `category` FROM `products` ");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $categories = array_map(function ($row) {//loop through the rows array
        return $row['category'];//return the category name to the categories array 
    }, $rows);
    $response['status'] = "success";
    $response['categories'] = $categories;
} catch (PDOException $e) {
    $response['status'] = "error";
    $response['message'] = "Error: " . $e->getMessage();
}
echo json_encode($response);
