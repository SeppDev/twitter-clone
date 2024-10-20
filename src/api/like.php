<?php
require "../modules/database.php";

$user = getUserSession();

if (!$user) {
    build_error("Not logged in");
}

$headers = getallheaders();
$postId = isset($headers['postId']) ? $headers['postId'] : build_error("No postId");
$newStatus = isset($headers['newStatus']) ? $headers['newStatus'] == "true" : build_error("No newStatus");

// build_error(json_encode($headers["newStatus"])); 

function like(int $postId, User $user, bool $requestedStatus)
{
    $connection = $GLOBALS["database"];
    $likeStatus = likeStatus($postId, $user->id);
    $newStatus = $likeStatus;


    if ($requestedStatus == false) {
        $query = $connection->prepare("DELETE FROM `likes` WHERE (`author`) LIKE ? AND link LIKE ? ");
        $query->bindParam(1, $user->id, PDO::PARAM_INT);
        $query->bindParam(2, $postId, PDO::PARAM_INT);

        if ($likeStatus == true) {
            $query->execute();
            $newStatus = false;
        }

    } else {
        $query = $connection->prepare("INSERT INTO `likes` (`author`, `link`) VALUES (?, ?)");
        $query->bindParam(1, $user->id, PDO::PARAM_INT);
        $query->bindParam(2, $postId, PDO::PARAM_INT);
        if ($likeStatus == false) { 
            $query->execute();
            $newStatus = true;
        }
    }

    $count = postLikes($postId);

    die(json_encode(array(
        "result" => $newStatus,
        "count" => $count,
    )));
}

like($postId, $user, $newStatus == true);