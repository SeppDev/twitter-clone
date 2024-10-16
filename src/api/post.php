<?php
require "../modules/database.php";

$user = getUserSession();
$content = $_POST["content"];

if (!$user) {
    build_error("Not logged in");
}

if (!$content) {
    build_error("No content");
}

$tweet = new tweet($content, $user->id);
$tweet->post();