<?php
require "../modules/database.php";

$user = getUserSession();

if (!$user) {
    build_error("Not logged in");
}

$headers = getallheaders();
$postId = isset($headers['postId']) ? $headers['postId'] : null;

if (!$postId) {
    build_error("No postId");
}

like($postId, $user);