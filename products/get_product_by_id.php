<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
    $id=secureRequest($_GET['id']);
    if(checkIfProductExists($id)){
        $stmt=$connection->prepare("SELECT * FROM products WHERE id=?");
        $stmt->execute(array($id));
        $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
        $response['product']=$rows;
    $response['status']="success";
    $response['message']="Product data retrieved successfully";
    }else{
        $response['status']="error";
        $response['message']="Product does not exist";
    }
 
}catch(PDOException $e){
    $response['status']="error";
    $response['message']="Error: ".$e->getMessage();
}
echo json_encode($response);