<?php
include "../connect.php";
include "../core.php";
$response = [];
try {
    $product_id = secureRequest($_POST['product_id']);
    $similarity_set_id = secureRequest($_POST['similarity_set_id']);
    $product = getProductById($product_id);
    $alreadyInSimilarity = searchForProductInSimilarity($product, $similarity_set_id);
    $haveAnotherSimilarity = doesProuctHaveSimilarity($product);
    if ($alreadyInSimilarity) {
        $response["status"] = "error";
        $response["message"] = "Product Already In Similarity";
    } else {
        if ($haveAnotherSimilarity) {
            removeProductFromSimilarProducts($product);
        }
        addProductToSimilaritySet($product_id, $similarity_set_id);
        $response["status"] = "success";
        $response["message"] = "Product Added To Similarity Successfully";
    }
} catch (PDOException $e) {
    $response["status"] = "error";
    $response["message"] = "Error: " . $e->getMessage();
}
echo json_encode($response);
