<?php
include "../connect.php";
include "../core.php";
$response = [];
try {
    $username = secureRequest($_POST['name']);
    $email = secureRequest($_POST['e-mail']);
    $password = secureRequest($_POST['password']);
    $address = secureRequest($_POST['address']);
    if (empty($username) || empty($email) || empty($password) || empty($address)) {
        $response['status'] = 'error';
        $response['message'] = 'Please fill all fields';
        echo json_encode($response);
        return;
    }
    if(checkIfEmailExists($email)){
        $response['status'] = 'error';
        $response['message'] = 'Email already exists';
        echo json_encode($response);
        return;
        }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid email format';
        echo json_encode($response);
        return;
    }
 
    
    $password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $connection->prepare("INSERT INTO `users`(`name`,`e-mail`,`password`,`address`) VALUES(?,?,?,?)");
    $stmt->execute(array(
        $username,
        $email,
        $password,
        $address
    ));
    $response['status'] = "success";
    $response["message"] = "User Added Successfully";
    $response["user"]=[
        "name"=>$username,
        "e-mail"=>$email,
        "address"=>$address
    ];
} catch (PDOException $e) {
    $response['status'] = "error";
    $response["message"] = "Error: " . $e->getMessage();
}
echo json_encode($response);
