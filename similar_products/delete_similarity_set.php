<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
$similiraty_set_id=secureRequest($_POST['similarity_set_id']);
$stmt=$connection->prepare("DELETE FROM similar_products WHERE id=?");
$stmt->execute(array($similiraty_set_id));
deleteSimilarityFromProducts($similiraty_set_id);
$response['status']="success";
$response['message']="Similarity Set Deleted Successfully";
}catch(PDOException $e){
    $response['status']="error";
    $response['message']="Error: ".$e->getMessage();
}
echo json_encode($response);