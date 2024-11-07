<?php
require "../modules/database.php";

$user = getUserSession();

if (!$user) {
    build_error("not logged in?");
}

$connection = $GLOBALS["database"];
$query = $connection->prepare("UPDATE `users` SET `banner_img` = ? WHERE `id` = ?");
$fileTmpPath = $_FILES["image"]['tmp_name'];
$fileData = file_get_contents($fileTmpPath);
$query->bindParam(1, $fileData, PDO::PARAM_STR);
$query->bindParam(2, $user->id, PDO::PARAM_INT);
$query->execute();