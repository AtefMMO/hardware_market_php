<?php
include "../connect.php";
include "../core.php";
$response = [];
try {
    $user_id = secureRequest($_POST['user_id']);
    $stmt = $connection->prepare("SELECT * FROM orders WHERE user_id=?");
    $stmt->execute([$user_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['status'] = "success";
    $response['message'] = "Orders retrieved successfully";
    $response['orders'] = $rows;
} catch (PDOException $e) {
    $response["status"] = "error";
    $response["message"] = "Invalid request";
}
echo json_encode($response);
