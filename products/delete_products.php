<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
    $id=secureRequest($_POST['id']);
    if(checkIfProductExists($id)){
        $stmt=$connection->prepare("DELETE FROM products WHERE id=?");
        $stmt->execute(array($id));
        $response['status']="success";
        $response['message']="Product Deleted Successfully";}
        else{
            $response['status']="error";
            $response['message']="Product does not exist";
        }
}catch(PDOException $e){
    $response['status']="error";
    $response['message']="Error: ".$e->getMessage();
}
echo json_encode($response);