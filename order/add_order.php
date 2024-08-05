<?php
include "../connect.php";
include "../core.php";
$response=[];
try{
    $user_id=secureRequest($_POST['user_id']);
   $products_ids= getCartProductsByUserId($user_id);
    if(count($products_ids)==0){
     $response['status']="error";
     $response['message']="Cart is empty";
     echo json_encode($response);
     exit();
    }
   $address=getUser($user_id)['address'];
   $coupon=secureRequest($_POST['coupon']);
   if($coupon != null){
    if(searchIfCopounExists($coupon)==false){
        $response['status']="error";
        $response['message']="Invalid coupon";
        echo json_encode($response);
        exit();
    }
   }
 
   $sale=getSale($coupon);
   $delivery_price=20;
   $items_price=calculateItemsPriceByProductsIds($products_ids);
    $total_price=getTotalPrice($items_price,$delivery_price,$sale);
    $order_status=secureRequest($_POST['order_status']);
    $stmt=$connection->prepare("INSERT INTO orders (user_id,products_ids,address,coupon,sale,delivery_price,items_price,total_price,order_status) VALUES (?,?,?,?,?,?,?,?,?)");
   $products_ids=json_encode($products_ids);
    $stmt->execute([$user_id,$products_ids,$address,$coupon,$sale,$delivery_price,$items_price,$total_price,$order_status]);
    $response['status']="success";
    $response['message']="Order added successfully";
    $response['data']=[
        "user_id"=>$user_id,
        "products_ids"=>$products_ids,
        "address"=>$address,
        "coupon"=>$coupon,
        "sale"=>$sale,
        "delivery_price"=>$delivery_price,
        "items_price"=>$items_price,
        "total_price"=>$total_price,
        "order_status"=>$order_status
    ];
    }
catch(PDOException $e){
 $response["status"]="error";
    $response["message"]="Invalid request";
}
echo json_encode($response);