<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
    $product_id= secureRequest($_POST['product_id']);
    if(!checkIfProductExistsInFeaturedProducts($product_id)){
        $response['status'] = "error";
        $response['message'] = "Product is not in featured products";
        echo json_encode($response);
        exit();
    }
    $stmt=$connection->prepare("DELETE FROM featured_products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $response['status'] = "success";
    $response['message'] = "Product deleted from featured products";
}catch(PDOException $e){
    $response['status'] = "error";
    $response['message'] = "Invalid request";
}
echo json_encode($response);