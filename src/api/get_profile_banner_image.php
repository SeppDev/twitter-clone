<?php
require "../modules/database.php";
function getProfileImage(int $userId): string
{
    $connection = $GLOBALS["database"];
    $query = $connection->prepare("SELECT `banner_img` FROM `users` WHERE id LIKE ?");
    $query->bindParam(1, $userId, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    header("Content-Type: image/*");
    if (isset($result['banner_img'])) {
        return $result["banner_img"];
    }
    return readRelativeFile("images/banner.jpeg");
}

if (isset($_GET["userid"])) {
    echo getProfileImage($_GET["userid"]);
} else {
    $currentUser = getUserSession();
    echo getProfileImage($currentUser->id);
}
