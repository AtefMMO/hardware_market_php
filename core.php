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
function checkLoginCredentials($email, $password)
{
    global $connection;
    $stmt = $connection->prepare("Select * from `users` where `e-mail`=?");
    $stmt->execute(array($email));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
   //if ($user!=null && password_verify($password,$user['password'] )) {
    return $user;    
   // } else {
   //    return false;
    //}
}
