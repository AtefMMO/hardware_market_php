<?php
include "../connect.php";
include "../core.php";
$response = [];
try {
    $product_id = secureRequest($_POST['product_id']);
    $product=getProductById($product_id);
    if($product['similar_products_id']==null){
        $response['status'] = "error";
        $response["message"] = "Product Not In Similarity";
        echo json_encode($response);
        return;
    }
    removeProductFromSimilarProducts($product);
    $response['status'] = "success";
    $response["message"] = "Product Removed From Similarity Successfully";
} catch (PDOException $e) {
    $response['status'] = "error";
    $response['message'] = "Error: " . $e->getMessage();
}
echo json_encode($response);