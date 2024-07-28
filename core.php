<?php
include "connect.php";
function secureRequest($request)
{
    return htmlspecialchars(strip_tags($request));
}
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
function checkLoginCredentials($email, $password)
{
    global $connection;
    $stmt = $connection->prepare("Select * from `users` where `e-mail`=?");
    $stmt->execute(array($email));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
   if ($user!=null && password_verify($password,$user['password'] )) {
    $token = bin2hex(random_bytes(50));
    $stmt = $connection->prepare("UPDATE `users` SET `token`=? WHERE `e-mail`=?");
    $stmt->execute(array($token, $email));
    $user['token'] = $token;
    return $user;    
    } else {
       return false;
    }
}
