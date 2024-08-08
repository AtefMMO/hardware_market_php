<?php
include "../connect.php";
include "../core.php";
$response = [];
try {
    $product_ids = json_decode($_POST['product_ids'], true);
    $simillarityName = secureRequest($_POST['simillarity_name']);
    $stmt = $connection->prepare("INSERT INTO `simillar_products`(products_ids, simillarity_name) VALUES (?, ?)");
    $stmt->execute(array(
        json_encode($product_ids),
        $simillarityName
    ));
    $id=$connection->lastInsertId();
    editProductsSimillaritySet($product_ids, $id);
    $response['status'] = "success";
    $response["message"] = "Simillar Product Set Created Successfully";
    $response["simillar_product_set"] = [
        "id" => $id,
        "product_ids" => $product_ids,
        "simillarity_name" => $simillarityName
    ];
    $simillarProducts = getSimillarProducts($id);
    $response["simillar_products"] = $simillarProducts;
} catch (PDOException $e) {
    $response['status'] = "error";
    $response['message'] = "Error: " . $e->getMessage();
}
echo json_encode($response);
