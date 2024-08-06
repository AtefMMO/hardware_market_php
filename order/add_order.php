<?php
include "../connect.php";
include "../core.php";
$response = [];
try {
    $user_id = secureRequest($_POST['user_id']);
    $products_ids = getCartProductsByUserId($user_id);
    if (count($products_ids) == 0) {
        $response['status'] = "error";
        $response['message'] = "Cart is empty";
        echo json_encode($response);
        exit();
    }
    $address = getUser($user_id)['address'];
    $coupon = secureRequest($_POST['coupon']);
    if ($coupon != null) {
        if (searchIfCopounExists($coupon) == false) {
            $response['status'] = "error";
            $response['message'] = "Invalid coupon";
            echo json_encode($response);
            exit();
        }
    }
    $sale = getSale($coupon);
    $delivery_price = 20;
    $paymentType = secureRequest($_POST['payment_type']);
    $cartProducts=getAllCartProducts($user_id);
    $productsQuantities=getCartProductsQuantities($cartProducts);
   
   $stockQunatities=getAllCartProducts($user_id);
   $stockQunatities=getStockQuantitiesByIds($products_ids);
  if(compareBtwStockAndCartQuantities($productsQuantities,$stockQunatities)==false){
       $response['status'] = "error";
        $response['message'] = "Not enough products in stock";
        echo json_encode($response);
       exit();
    }
    $items_price = calculateItemsPrice($cartProducts);
    $total_price = getTotalPrice($items_price, $delivery_price, $sale);
    $order_status = secureRequest($_POST['order_status']);
    $stmt = $connection->prepare("INSERT INTO orders (user_id,products_ids,address,coupon,sale,delivery_price,items_price,total_price,order_status,payment_type,products_quantities) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([$user_id, json_encode($products_ids), $address, $coupon, $sale, $delivery_price, $items_price, $total_price, $order_status, $paymentType,json_encode($productsQuantities)]);
    eraseCart($user_id);
    $response['status'] = "success";
    $response['message'] = "Order added successfully";
    $response['data'] = [
        "user_id" => $user_id,
        "products_ids" => $products_ids,
        "products_quantities" => $productsQuantities,
        "address" => $address,
        "coupon" => $coupon,
        "sale" => $sale,
        "delivery_price" => $delivery_price,
        "items_price" => $items_price,
        "total_price" => $total_price,
        "order_status" => $order_status,
        "payment_type" => $paymentType
    ];
} catch (PDOException $e) {
    $response["status"] = "error";
    $response["message"] = "Invalid request".$e->getMessage();
}
echo json_encode($response);
