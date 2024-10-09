<?php
require "../modules/database.php";
$headers = getallheaders();

$content = isset($headers['content']) ? $headers['content'] : null;
$user = getUserSession();

if (!$content) {
    build_error("No content");
}

$tweet = new tweet($content, $user->id);
$tweet->post();