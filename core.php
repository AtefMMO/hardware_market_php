<?php
include "connect.php";
define('MB', 1048576);
//-----------------------------------------------------------------------------------------------------------
//secure_requests_functions
function secureRequest($request)
{
    return htmlspecialchars(strip_tags($request));
}

//-----------------------------------------------------------------------------------------------------------
//upload_functions
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
//-----------------------------------------------------------------------------------------------------------
//products_functions
function getProductById($id)
{
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM `products` WHERE `id` = ?");
    $stmt->execute(array($id));
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    return $product;
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


//-----------------------------------------------------------------------------------------------------------
//user_functions
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
function getUser($user_id)
{
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM `users` WHERE `id`=?");
    $stmt->execute(array($user_id));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user;
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
function checkLoginCredentialsMobile($email, $password)
{
    global $connection;
    $stmt = $connection->prepare("Select * from `users` where `e-mail`=?");
    $stmt->execute(array($email));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user != null && password_verify($password, $user['password'])) {
        $mobileToken = bin2hex(random_bytes(50));
        $stmt = $connection->prepare("UPDATE `users` SET `mobile_token`=? WHERE `e-mail`=?");
        $stmt->execute(array($mobileToken, $email));
        $user['mobile_token'] = $mobileToken;
        return $user;
    } else {
        return false;
    }
}
function checkOldPassword($id, $old_password)
{
    global $connection;
    $stmt = $connection->prepare("Select * from `users` where `id`=?");
    $stmt->execute(array($id));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user != null && password_verify($old_password, $user['password'])) {
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
//-----------------------------------------------------------------------------------------------------------
//coupon_functions
function searchIfCopounExists($coupon)
{
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM `coupons` WHERE `coupon`=?");
    $stmt->execute(array($coupon));
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($coupon != null) {
        return true;
    } else {
        return false;
    }
}
//-----------------------------------------------------------------------------------------------------------
//order_functions
function getStockQuantitiesByIds($productsIds)
{
    global $connection;
    $quantities = [];
    for ($i = 0; $i < count($productsIds); $i++) {
        $stmt = $connection->prepare("SELECT * FROM `products` WHERE `id` = ?");
        $stmt->execute(array($productsIds[$i]));
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        $quantities[$i] = $product['quantity'];
    }
    return $quantities;
}
function getSale($coupon)
{
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM `coupons` WHERE `coupon`=?");
    $stmt->execute(array($coupon));
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($coupon != null) {
        return $coupon["value"];
    } else {
        return false;
    }
}

function calculateItemsPrice($products)
{
    $totalPrice = 0;
    for ($i = 0; $i < count($products); $i++) {
        $product = getProductById($products[$i]['product_id']);
        $productPrice = $product['price'];
        $totalPrice += $productPrice * $products[$i][3];
    }
    return $totalPrice;
}
function getTotalPrice($items_price, $delivery_price, $sale)
{
    $totalPrice = $items_price;
    if ($sale != false) {
        $totalPrice = $totalPrice - $sale;
    }
    return $totalPrice + $delivery_price;
}
function getOrderById($order_id)
{
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM `orders` WHERE `id`=?");
    $stmt->execute(array($order_id));
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    return $order;
}
function getOrdersByUserId($user_id)
{
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM `orders` WHERE `user_id`=?");
    $stmt->execute(array($user_id));
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $orders;
}
function updateOrderStatus($order_id, $status)
{
    global $connection;
    $stmt = $connection->prepare("UPDATE `orders` SET `order_status`=? WHERE `id`=?");
    $stmt->execute(array($status, $order_id));
}
//-----------------------------------------------------------------------------------------------------------
//favourits_functions

function checkIfProductAlreadyFavourit($user_id, $product_id)
{
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM `favourits` WHERE `user_id`=? AND `product_id`=?");
    $stmt->execute(array($user_id, $product_id));
    if ($stmt->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}

//-----------------------------------------------------------------------------------------------------------
//address_functions
function getUserAdresses($user_id)//get all user addresses
{
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM `addresses` WHERE `user_id`=?");
    $stmt->execute(array($user_id));
    $adresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $adresses;
}
function getUserAdressesCount($user_id)//get number of user addresses
{
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM `addresses` WHERE `user_id`=?");
    $stmt->execute(array($user_id));
    $adresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return count($adresses);
}
function getAddress($address_id)//get address by id
{
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM `addresses` WHERE `id`=?");
    $stmt->execute(array($address_id));
    $address = $stmt->fetch(PDO::FETCH_ASSOC);
    return $address;
}
function checkIfAddressIsPrimary($address_id)//check if address is primary
{
    $address = getAddress($address_id);
    if ( $address['is_primary_address'] == 1) {
        return true;
    } else {
        return false;
    }
}
function deleteAddress($address_id,$user_id)//delete address
{
    removeAddressFromUserIfPrimary($address_id,$user_id);
    global $connection;
    $stmt = $connection->prepare("DELETE FROM `addresses` WHERE `id`=?");
    $stmt->execute(array($address_id));
}
function removeAddressFromUserIfPrimary($address_id,$user_id)//remove address from user if primary
{
    if(checkIfAddressIsPrimary($address_id)){
        setAddressToNotPrimary($user_id);
    }
}
function checkIfUserHavePrimaryAddress($user_id)//check if user has primary address
{
    $addresses = getUserAdresses($user_id);
    for ($i = 0; $i < count($addresses); $i++) {
        if ($addresses[$i]['is_primary_address'] == 1) {
            return true;
        }
    }
    return false;
}
function setAddressToNotPrimary($user_id)//set address to not primary at user table
{
  global $connection;
    $stmt = $connection->prepare("UPDATE `users` SET `primary_address_id`=null WHERE `id`=?");
    $stmt->execute(array($user_id));
}
function setOtherAddressesToNotPrimary($user_id, $address_id)//set other addresses to not primary
{
    global $connection;
    $addresses = getUserAdresses($user_id);
    for ($i = 0; $i < count($addresses); $i++) {
        if ($addresses[$i]['id'] != $address_id) {
            $stmt = $connection->prepare("UPDATE `addresses` SET `is_primary_address`=0 WHERE `id`=?");
            $stmt->execute(array($addresses[$i]['id']));
        }
    }
}
function setAddressToPrimary($user_id, $address_id)//set address to primary
{
    global $connection;
    setOtherAddressesToNotPrimary($user_id, $address_id);
    $stmt = $connection->prepare("UPDATE `addresses` SET `is_primary_address`=1 WHERE `id`=?");
    $stmt->execute(array($address_id));
    $stmt = $connection->prepare("UPDATE `users` SET `primary_address_id`=$address_id WHERE `id`=?");
    $stmt->execute(array($user_id));
}
function getPrimaryAddressId($user_id)//get primary address id
{
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM `addresses` WHERE `user_id`=? AND `is_primary_address`=1");
    $stmt->execute(array($user_id));
    $address = $stmt->fetch(PDO::FETCH_ASSOC);
    return $address['id'];
}
//-----------------------------------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------------------------------
//cart_functions
function getCartProductsByUserId($user_id)
{
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM `cart` WHERE `user_id` = ?");
    $stmt->execute(array($user_id));
    $cart = $stmt->fetchAll();
    $products = [];
    for ($i = 0; $i < count($cart); $i++) {
        $product = getProductById($cart[$i]['product_id']);
        array_push($products, $product["id"]);
    }
    return $products;
}
function getAllCartProducts($user_id)
{
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM `cart` WHERE `user_id` = ?");
    $stmt->execute(array($user_id));
    $cart = $stmt->fetchAll();
    return $cart;
}
function getCartProductsQuantities($products)
{
    $quantities = [];
    for ($i = 0; $i < count($products); $i++) {
        $quantities[$i] = $products[$i][3];
    }
    return $quantities;
}
function compareBtwStockAndCartQuantities($orderQuantities, $stockQuantities)
{
    for ($i = 0; $i < count($orderQuantities); $i++) {
        if ($orderQuantities[$i] > $stockQuantities[$i]) {
            return false;
        }
    }
    return true;
}
function eraseCart($user_id)
{
    global $connection;
    $stmt = $connection->prepare("DELETE FROM `cart` WHERE `user_id`=?");
    $stmt->execute(array($user_id));
}

function getProductFromCartById($user_id, $product_id)
{
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM `cart` WHERE `user_id`=? AND `product_id`=?");
    $stmt->execute(array($user_id, $product_id));
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    return $product;
}
function decreaseProductQuantityInCart($user_id, $product_id)
{
    global $connection;
    $cart = getProductFromCartById($user_id, $product_id);
    $stmt = $connection->prepare("UPDATE `cart` SET `quantity`=? WHERE `user_id`=? AND `product_id`=?");
    $stmt->execute(array($cart['quantity'] - 1, $user_id, $product_id));
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    return $product;
}
function deleteAllProductsInCart($user_id)
{
    global $connection;
    $stmt = $connection->prepare("DELETE FROM `cart` WHERE `user_id`=?");
    $stmt->execute(array($user_id));
}
//-----------------------------------------------------------------------------------------------------------
//similar_products functions
function removeProductsFromTheirSimilaritySets($products_ids){//remove products from their similarity sets when creating a new one
    for($i=0;$i<count($products_ids);$i++){
        $product = getProductById($products_ids[$i]);
if($product['similar_products_id']!=null){
    removeProductFromSimilarProducts($product);
    }
}
}
function addProductsToSimilaritySet($products_ids, $similarityName){//create similarity set
    global $connection;
    $stmt = $connection->prepare("INSERT INTO `similar_products`(`products_ids`, `similarity_name`) VALUES (?,?)");
    $stmt->execute(array(json_encode($products_ids), $similarityName));
    $id=$connection->lastInsertId();
    for($i=0;$i<count($products_ids);$i++){
        $stmt = $connection->prepare("UPDATE `products` SET `similar_products_id`=? WHERE `id`=?");
        $stmt->execute(array($id, $products_ids[$i]));
    }
}
function getSimilarProducts($similaritySetId)//get products in a similarity set
{
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM `products` WHERE `similar_products_id`=?");
    $stmt->execute(array($similaritySetId));
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $products;
}
function getSimilarProductsByProductId($product_id)//get products similar to a product
{
    global $connection;
    $product = getProductById($product_id);
    $stmt = $connection->prepare("SELECT * FROM `products` WHERE `similar_products_id`=? AND `id`!=?");
    $stmt->execute(array($product['similar_products_id'], $product_id));
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $products;
}
function getSimilarProductsSet($set_id)//get similarity set by id
{
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM `similar_products` WHERE `id`=?");
    $stmt->execute(array($set_id));
    $set = $stmt->fetch(PDO::FETCH_ASSOC);
    return $set;
}
function removeProductFromSimilarProducts($product)//remove product from similarity set
{
    global $connection;

    $set = getSimilarProductsSet($product['similar_products_id']);
    $products = json_decode($set['products_ids']);
    $index = array_search($product['id'], $products);
    array_splice($products, $index, 1);
    $stmt = $connection->prepare("UPDATE `similar_products` SET `products_ids`=? WHERE `id`=?");
    $stmt->execute(array(json_encode($products), $product['similar_products_id']));
    $stmt = $connection->prepare("UPDATE `products` SET `similar_products_id`=null WHERE `id`=?");
    $stmt->execute(array($product['id']));
}
function deleteSimilarityFromProducts($set_id)//delete similar_prouducts_id from products
{
    global $connection;
    $stmt = $connection->prepare("UPDATE `products` SET `similar_products_id`=null WHERE `similar_products_id`=?");
    $stmt->execute(array($set_id));
}
function showSimilaritySets()//get all similarity sets
{
    global $connection;
    $stmt = $connection->prepare("SELECT * FROM `similar_products`");
    $stmt->execute();
    $sets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $sets;
}
function searchForProductInSimilarity($product, $set_id)//search if product is in a similarity set
{
    if ($product['similar_products_id'] == $set_id) {
        return true;
    } else {
        return false;
    }
}
function doesProuctHaveSimilarity($product)//check if product has a similarity set
{

    if ($product['similar_products_id'] != null) {
        return true;
    } else {
        return false;
    }
}
function addProductToSimilaritySet($product_id, $set_id)//add product to similarity set
{
    $products = getSimilarProductsSet($set_id);
    $products = json_decode($products['products_ids']);
    array_push($products, (int)$product_id);
    global $connection;
    $stmt = $connection->prepare("UPDATE `similar_products` SET `products_ids`=? WHERE `id`=?");
    $stmt->execute(array(json_encode($products), $set_id));
    $stmt = $connection->prepare("UPDATE `products` SET `similar_products_id`=? WHERE `id`=?");
    $stmt->execute(array($set_id, $product_id));
}

//-----------------------------------------------------------------------------------------------------------