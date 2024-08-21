<?php
include "../connect.php";
include "../core.php";
$response = [];
try {
    $product_ids = json_decode($_POST['product_ids'], true);
    $similarityName = $_POST['similarity_name'];
    removeProductsFromTheirSimilaritySets($product_ids);
    addProductsToSimilaritySet($product_ids,$similarityName);
    $id=$connection->lastInsertId();
    $response['status'] = "success";
    $response["message"] = "Similar Product Set Created Successfully";
    $response["similar_product_set"] = [
        "id" => $id,
        "product_ids" => $product_ids,
        "similarity_name" => $similarityName
    ];
} catch (PDOException $e) {
    $response['status'] = "error";
    $response['message'] = "Error: " . $e->getMessage();
}
echo json_encode($response);
