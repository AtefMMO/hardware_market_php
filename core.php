<?php
include "connect.php";
define('MB', 1048576);
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
    if ($user != null && password_verify($password, $user['password'])) {
        $token = bin2hex(random_bytes(50));
        $stmt = $connection->prepare("UPDATE `users` SET `token`=? WHERE `e-mail`=?");
        $stmt->execute(array($token, $email));
        $user['token'] = $token;
        return $user;
    } else {
        return false;
    }
}
function checkToken($token)
{
    global $connection;
    $stmt = $connection->prepare("Select * from `users` where `token`=?");
    $stmt->execute(array($token));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user != null) {
        return $user;
    } else {
        return false;
    }
}
function uploadFile($file)
{
    $errorLargeSize = "File is too big";
    $errorUpload = "There was an error uploading the file";
    $errorWrongExtension = "You cannot upload files of this type";
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $allowExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
    $fileExt = explode('.', $fileName);
    $fileExt = strtolower(end($fileExt));
    if (in_array($fileExt, $allowExtensions) && !empty($fileName)) {
        if ($fileError === 0) {
            if ($fileSize < 10 * MB) {
                $fileName = uniqid('', true) . "." . $fileExt;
                $fileDestination = '../uploads/' . $fileName;
                move_uploaded_file($fileTmpName, $fileDestination);
                return $fileName;
            } else {
                return $errorLargeSize;
            }
        } else {
            return $errorUpload;
        }
    } else {
        return $errorWrongExtension;
    }
}
