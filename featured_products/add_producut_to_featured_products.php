<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
    $product_id= secureRequest($_POST['product_id']);
    if(!checkIfProductExists($product_id)){
        $response['status'] = "error";
        $response['message'] = "Product is not in the database";
        echo json_encode($response);
        exit();
    }
    if(checkIfProductExistsInFeaturedProducts($product_id)){
        $response['status'] = "error";
        $response['message'] = "Product already in featured products";
        echo json_encode($response);
        exit();
    }
    $stmt=$connection->prepare("INSERT INTO featured_products (product_id) VALUES (?)");
    $stmt->execute([$product_id]);
    $response['status'] = "success";
    $response['message'] = "Product added to featured products";
}catch(PDOException $e){
    $response['status'] = "error";
    $response['message'] = "Invalid request";
}
echo json_encode($response);