<?php
require "../modules/database.php";

$userId = isset($_GET["userid"]) ? $_GET["userid"] : readRelativeFile("../images/defaultpfp.jpeg");

function getProfileImage(int $userId): string
{
    $connection = $GLOBALS["database"];
    $query = $connection->prepare("SELECT `profile_img` FROM `users` WHERE id LIKE ?");
    $query->bindParam(1, $userId, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    header("Content-Type: image/*");
    if (isset($result['profile_img'])) {
        return $result["profile_img"];
    }
    return readRelativeFile("images/defaultpfp.jpeg");
}

echo getProfileImage($userId);
