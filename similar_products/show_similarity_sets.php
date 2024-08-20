<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
$sets=showSimilaritySets(  );
$response['status']="success";
$response["message"]= "Similar Products Retrieved Successfully";
$response['sets']=$sets;
}catch(PDOException $e){
    $response['status']="error";
    $response['message']="Error: ".$e->getMessage();
}   
echo json_encode($response);