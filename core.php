<?php
include "connect.php";
define('MB', 1048576);
function secureRequest($request)
{
    return htmlspecialchars(strip_tags($request));
}
function checkIfEmailExists($email)
{
    global $connection;
    $stmt = $connection->prepare("Select * from `users` where `e-mail`=?");
    $stmt->execute(array($email));
    if ($stmt->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}
function checkIfProductExists($id)
{
    global $connection;
    $stmt = $connection->prepare("Select * from `products` where `id`=?");
    $stmt->execute(array($id));
    if ($stmt->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}
function checkLoginCredentials($email, $password)
{
    global $connection;
    $stmt = $connection->prepare("Select * from `users` where `e-mail`=?");
    $stmt->execute(array($email));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user != null && password_verify($password, $user['password'])) {
        $token = bin2hex(random_bytes(50));
        $stmt = $connection->prepare("UPDATE `users` SET `token`=? WHERE `e-mail`=?");
        $stmt->execute(array($token, $email));
        $user['token'] = $token;
        return $user;
    } else {
        return false;
    }
}
function checkToken($token)
{
    global $connection;
    $stmt = $connection->prepare("Select * from `users` where `token`=?");
    $stmt->execute(array($token));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user != null) {
        return $user;
    } else {
        return false;
    }
}
function uploadFile($file)
{
    $errorLargeSize = "File is too big";
    $errorUpload = "There was an error uploading the file";
    $errorWrongExtension = "You cannot upload files of this type";
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $allowExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
    $fileExt = explode('.', $fileName);
    $fileExt = strtolower(end($fileExt));
    if (in_array($fileExt, $allowExtensions) && !empty($fileName)) {
        if ($fileError === 0) {
            if ($fileSize < 10 * MB) {
                $fileName = uniqid('', true) . "." . $fileExt;
                $fileDestination = '../uploads/' . $fileName;
                move_uploaded_file($fileTmpName, $fileDestination);
                return $fileName;
            } else {
                return $errorLargeSize;
            }
        } else {
            return $errorUpload;
        }
    } else {
        return $errorWrongExtension;
    }
}
function getProductById($id){
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM `products` WHERE `id` = ?");
    $stmt->execute(array($id));
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    return $product;
}
function getCartProductsByUserId($user_id){
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM `cart` WHERE `user_id` = ?");
    $stmt->execute(array($user_id));
    $cart = $stmt->fetchAll();
    $products = [];
    for($i=0;$i<count($cart);$i++){
        $product = getProductById($cart[$i]['product_id']);
        array_push($products,$product["id"]);
    }
    return $products;
}
function getAllCartProducts($user_id){
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM `cart` WHERE `user_id` = ?");
    $stmt->execute(array($user_id));
    $cart = $stmt->fetchAll();
    return $cart;
}
function getStockQuantitiesByIds($productsIds){
global $connection;
$quantities=[];
for($i=0;$i<count($productsIds);$i++){
    $stmt = $connection->prepare("SELECT * FROM `products` WHERE `id` = ?");
    $stmt->execute(array($productsIds[$i]));
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    $quantities[$i]=$product['quantity'];
}
return $quantities;
}
function getSale($coupon){
    global $connection;
    $stmt=$connection->prepare("SELECT * FROM `coupons` WHERE `coupon`=?");
    $stmt->execute(array($coupon));
    $coupon=$stmt->fetch(PDO::FETCH_ASSOC);
    if($coupon!=null){
        return $coupon["value"];
    }else{
        return false;
    }
}
function getCartProductsQuantities($products){
    $quantities=[];
    for($i=0;$i<count($products);$i++){
        $quantities[$i]=$products[$i][3];
    }
    return $quantities;
}
function calculateItemsPrice($products){
    $totalPrice=0;
    for($i=0;$i<count($products);$i++){
       $product = getProductById($products[$i]['product_id']);
         $productPrice=$product['price'];
        $totalPrice+=$productPrice*$products[$i][3];
    }
    return $totalPrice;
}

function getTotalPrice($items_price,$delivery_price,$sale){
    $totalPrice=$items_price;
    if($sale!=false){
        $totalPrice=$totalPrice-$sale;
    }
    return $totalPrice+$delivery_price;
}
function getUser($user_id){
    global $connection;
    $stmt=$connection->prepare("SELECT * FROM `users` WHERE `id`=?");
    $stmt->execute(array($user_id));
    $user=$stmt->fetch(PDO::FETCH_ASSOC);
    return $user;
}
function searchIfCopounExists($coupon){
    global $connection;
    $stmt=$connection->prepare("SELECT * FROM `coupons` WHERE `coupon`=?");
    $stmt->execute(array($coupon));
    $coupon=$stmt->fetch(PDO::FETCH_ASSOC);
    if($coupon!=null){
        return true;    
    }else{
        return false;
    }
    
}
function getOrderById($order_id){
    global $connection;
    $stmt=$connection->prepare("SELECT * FROM `orders` WHERE `id`=?");
    $stmt->execute(array($order_id));
    $order=$stmt->fetch(PDO::FETCH_ASSOC);
    return $order;
}
function getOrdersByUserId($user_id){
    global $connection;
    $stmt=$connection->prepare("SELECT * FROM `orders` WHERE `user_id`=?");
    $stmt->execute(array($user_id));
    $orders=$stmt->fetchAll(PDO::FETCH_ASSOC);
    return $orders;
}
function eraseCart($user_id){
    global $connection;
    $stmt=$connection->prepare("DELETE FROM `cart` WHERE `user_id`=?");
    $stmt->execute(array($user_id));
}

function compareBtwStockAndCartQuantities($orderQuantities,$stockQuantities){
    for($i=0;$i<count($orderQuantities);$i++){
        if($orderQuantities[$i]>$stockQuantities[$i]){
            return false;
        }
    }
    return true;
}
function checkIfProductAlreadyFavourit($user_id,$product_id){
    global $connection;
    $stmt=$connection->prepare("SELECT * FROM `favourits` WHERE `user_id`=? AND `product_id`=?");
    $stmt->execute(array($user_id,$product_id));
    if($stmt->rowCount()>0){
        return true;
    }else{
        return false;
    }
}