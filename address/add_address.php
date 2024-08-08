<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
    $user_id = secureRequest($_POST['user_id']);
    $addressDetails = secureRequest($_POST['address_details']);
    $city = secureRequest($_POST['city']);
    $country = secureRequest($_POST['country']);
    $postalCode = secureRequest($_POST['postal_code']);
    $is_brimary_address = secureRequest($_POST['is_primary_address']);
    if(empty($user_id) || empty($addressDetails) || empty($city) || empty($country) || empty($postalCode) ){
        $response['status'] = 'error';
        $response['message'] = 'Please fill all fields';
        echo json_encode($response);
        return;
    }
    if($is_brimary_address != 1 && $is_brimary_address != 0){
       $response['status'] = 'error';
         $response['message'] = 'Invalid primary address value';
            echo json_encode($response);
            return;
    }
    if(getUserAdressesCount($user_id)>2){
        $response['status'] = 'error';
        $response['message'] = 'You can not add more than 3 addresses';
        echo json_encode($response);
        return;
    }
    if($is_brimary_address == 1){
       if(checkIfUserHavePrimaryAddress($user_id)==true){
           $response['status'] = 'error';
           $response['message'] = 'You can not add more than one primary address';
           echo json_encode($response);
           return;
       }
    }
    $stmt = $connection->prepare("INSERT INTO `addresses`(`user_id`, `address_details`, `city`, `country`, `postal_code`,`is_primary_address`) VALUES (?,?,?,?,?,?)");
    $stmt->execute(array($user_id, $addressDetails,$city, $country, $postalCode, $is_brimary_address));
    $response['status'] = "success";
    $response["message"] = "Address added successfully";
    $response["address"] = [
        "user_id" => $user_id,
        "address_details" => $addressDetails,
        "city" => $city,
        "country" => $country,
        "postal_code" => $postalCode,
        "is_primary_address" => $is_brimary_address
    ];
}catch(PDOException $e){
    $response["status"]="error";
    $response["message"]="Invalid request";
}
echo json_encode($response);