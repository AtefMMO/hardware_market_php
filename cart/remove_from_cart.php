<?php
include "../connect.php";
include "../core.php";
$response = [];
try {
    $product_id = secureRequest($_POST['product_id']);
    $user_id = secureRequest($_POST['user_id']);
    $product = getProductFromCartById( $user_id, $product_id);
    if (!$product) {
        $response['status'] = "error";
        $response['message'] = "Product not found in cart";
        echo json_encode($response);
        exit();
    }
    if ($product['quantity'] == 1) {
        $stmt = $connection->prepare("DELETE FROM cart WHERE product_id=? AND user_id=?");
        $stmt->execute([$product_id, $user_id]);
        $response['status'] = "success";
        $response['message'] = "Product removed from cart";
        echo json_encode($response);
        exit();
    }
    decreaseProductQuantityInCart( $user_id, $product_id);
    $response['status'] = "success";
    $response['message'] = "Product quantity decreased";
} catch (PDOException $e) {
    $response["status"] = "error";
    $response["message"] = "Invalid request" . $e->getMessage();
}
echo json_encode($response);