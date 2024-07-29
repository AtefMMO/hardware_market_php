<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
    $name = secureRequest($_POST['name']);
    $price = secureRequest($_POST['price']);
    $description = secureRequest($_POST['description']);
    $image = secureRequest($_POST['image']);
    $category = secureRequest($_POST['category']);
    $color = secureRequest($_POST['color']);
    $sale = secureRequest($_POST['sale']);
    $quantity = secureRequest($_POST['quantity']);
    $availability = secureRequest($_POST['availability']);
    $reference = secureRequest($_POST['reference']);
    $id=secureRequest($_POST['id']);
    $stmt=$connection->prepare("UPDATE `products` SET `name`=?,`price`=?,`description`=?,`image`=?,`category`=?,`color`=?,`sale`=?,`quantity`=?,`availability`=?,`reference`=? WHERE id=?");
    $stmt->execute(array(
        $name,
        $price,
        $description,
        $image,
        $category,
        $color,
        $sale,
        $quantity,
        $availability,
        $reference,
        $id
    ));
    $response['status'] = "success";
    $response["message"] = "Product Updated Successfully";
    $response["product"] = [
        "name" => $name,
        "price" => $price,
        "description" => $description,
        "image" => $image,
        "category" => $category,
        "color" => $color,
        "sale" => $sale,
        "quantity" => $quantity,
        "availability" => $availability,
        "reference" => $reference
    ];
}catch(PDOException $e){
    $response['status'] = "error";
    $response['message'] = "Error: ".$e->getMessage();
}
echo json_encode($response);