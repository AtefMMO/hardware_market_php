<?php
include "../connect.php";
include "../core.php";
$response = [];
try {
    $order_id = secureRequest($_POST['order_id']);
    $order = getOrderById($order_id);
    if ($order == null) {
        $response['status'] = "error";
        $response['message'] = "Order not found";
        echo json_encode($response);
        exit();
    } elseif ($order['order_status'] != "pending") {
        $response['status'] = "error";
        $response['message'] = "Order can't be removed";
        echo json_encode($response);
        exit();
    } else {
        $stmt = $connection->prepare("DELETE FROM orders WHERE id=?");
        $stmt->execute([$order_id]);
        $response['status'] = "success";
        $response['message'] = "Order removed successfully";
    }
} catch (PDOException $e) {
    $response["status"] = "error";
    $response["message"] = "Invalid request";
}
echo json_encode($response);
