<?php
include "../connect.php";
include "../core.php";
$response = [];
try {
    $token = secureRequest($_POST['token']);
    $user = checkToken($token);
    if ($user != false) {
        $stmt = $connection->prepare("SELECT * FROM products");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response['status'] = "success";
        $response['products'] = $rows;
    } else {
        $response['status'] = "error";
        $response['message'] = "Invalid token";
    }
} catch (PDOException $e) {
    $response['status'] = "error";
    $response['message'] = "Error: " . $e->getMessage();
}
