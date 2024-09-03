<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
   $products= getFeaturedProducts();

    if($products){
         $response['status'] = "success";
         $response['products'] = $products;
    }else{
        $response['status'] = "error";
        $response['message'] = "No featured products";
    }
}catch(PDOException $e){
    $response['status'] = "error";
    $response['message'] = "Invalid request";
}
echo json_encode($response);