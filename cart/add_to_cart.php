<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
$user_id = secureRequest($_POST['user_id']);
$product_id = secureRequest($_POST['product_id']);
$cart=getCartProductsByUserId($user_id);
for($i=0;$i<count($cart);$i++){
    if($cart[$i]==$product_id){
       $stmt = $connection->prepare("UPDATE `cart` SET quantity=quantity+1 WHERE product_id=? AND user_id=?");
         $stmt->execute(array($product_id,$user_id));
            $response['status']="success";
            $response['message']="Product added to cart";
            echo json_encode($response);
            exit();
    }
}
$product =getProductById($product_id);
if($product['quantity']<=0){
    $response['status']="error";
    $response['message']="Product out of stock";
    echo json_encode($response);
    exit();
}
$quantity = 1;
$stmt = $connection->prepare("INSERT INTO cart (product_id,user_id,quantity) VALUES (?,?,?)");
$stmt->execute(array($product_id,$user_id,$quantity));
$product=getProductById($product_id);
$response['status']="success";  
$response['message']="Product added to cart";
}catch(PDOException $e){
    $response["status"]="error";
    $response["message"]="Invalid request";
}
echo json_encode($response);