<?php
include("../connect.php");
include("../core.php");
$response=[];
try{
$product_id=json_decode($_GET['product_id']);
$products=getSimilarProductsByProductId( $product_id );
$response['status']="success";
$response["message"]= "Similar Products Retrieved Successfully";
$response['products']=$products;
}catch(PDOException $e){
    $response['status']="error";
    $response['message']="Error: ".$e->getMessage();
}
echo json_encode($response);