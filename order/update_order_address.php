<?php
include "../connect.php";
include "../core.php";
$response = [];
try {
    $order_id=secureRequest($_POST['order_id']);
    $address_id=secureRequest($_POST['address_id']);    
    $order=getOrderById($order_id);
    if($order['order_status']!="pending"){
        $response['status'] = "error";
        $response['message'] = "Order can't be updated";
        echo json_encode($response);
        exit();
    }
    if($order['address_id']==$address_id){
        $response['status'] = "error";
        $response['message'] = "Order already has this address";
        echo json_encode($response);
        exit();
    }
    $stmt = $connection->prepare("UPDATE orders SET address_id=? WHERE id=?");
    $stmt->execute([$address_id,$order_id]);
    $response['status'] = "success";
    $response['message'] = "Order updated successfully";
    $response['order'] = getOrderById($order_id);
} catch (PDOException $e) {
    $response["status"] = "error";
    $response["message"] = "Invalid request".$e->getMessage();
}
echo json_encode($response);
