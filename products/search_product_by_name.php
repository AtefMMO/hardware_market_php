<?php
include "../connect.php";
include "../core.php";
$response = [];
try {
    $query = secureRequest($_GET["name"]);
    $stmt = $connection->prepare("SELECT * FROM `products` WHERE `name` Like ?");
    $stmt->execute(array("%$query%"));//% is a wildcard character to match items with similar names
    $response["status"] = "success";
    $response["message"] = "Product found";
    $response["products"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $response["status"] = "error";
    $response["message"] = "Error:" . $e->getMessage();
}
echo json_encode($response);
