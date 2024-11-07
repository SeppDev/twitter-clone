<?php
require "../modules/database.php";

$user = getUserSession();

if (!$user) {
    build_error("not logged in?");
}

$username = $_POST["username"];
$description = $_POST["description"];

$connection = $GLOBALS["database"];
$query = $connection->prepare("UPDATE `users` SET `username` = ?, `description` = ? WHERE `id` = ?");
$query->bindParam(1, $username, PDO::PARAM_STR);
$query->bindParam(2, $description, PDO::PARAM_STR);
$query->bindParam(3, $user->id, PDO::PARAM_INT);
$query->execute();