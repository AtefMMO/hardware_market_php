<?php
include"connect.php";
$stmt=$connection->prepare("SELECT * FROM users");
$stmt->execute();
$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
echo json_encode($rows);
