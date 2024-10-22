<?php
require "../modules/database.php";

$token = getUserSessionToken();
if (!$token) {
    build_error("Not logged in");
}

$connection = $GLOBALS["database"];
$query = $connection->prepare("DELETE FROM `sessions` WHERE `sessions`.`token` LIKE ?");
$query->bindParam(1, $token, PDO::PARAM_STR);
try {
    $query->execute();
} catch (PDOException $e) {
    build_error($e->getMessage());
}