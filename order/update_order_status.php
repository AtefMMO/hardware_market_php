<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
    $order_id=secureRequest($_POST['order_id']);
    $status=secureRequest($_POST['status']);
    updateOrderStatus($order_id,$status);
    $response['status'] = "success";
    $response["message"] = "Order Status Updated Successfully";
}catch(PDOException $e){
    $response['status'] = "error";
    $response['message'] = "Error: ".$e->getMessage();
}
echo json_encode($response);