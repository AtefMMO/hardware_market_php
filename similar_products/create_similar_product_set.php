<?php
include "../connect.php";
include "../core.php";
$response = [];
try {
    $product_ids = json_decode($_POST['product_ids'], true);
    $similarityName = secureRequest($_POST['similarity_name']);
    $stmt = $connection->prepare("INSERT INTO `similar_products`(products_ids, similarity_name) VALUES (?, ?)");
    $stmt->execute(array(
        json_encode($product_ids),
        $similarityName
    ));
    $id=$connection->lastInsertId();
    editProductsSimilaritySet($product_ids, $id);
    $response['status'] = "success";
    $response["message"] = "Similar Product Set Created Successfully";
    $response["similar_product_set"] = [
        "id" => $id,
        "product_ids" => $product_ids,
        "similarity_name" => $similarityName
    ];
    $similarProducts = getSimilarProducts($id);
    $response["similar_products"] = $similarProducts;
} catch (PDOException $e) {
    $response['status'] = "error";
    $response['message'] = "Error: " . $e->getMessage();
}
echo json_encode($response);
