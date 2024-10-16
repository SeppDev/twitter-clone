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

if ($_FILES["file"]) {
    $filename = $_FILES["file"]["name"];
    $file = file_get_contents($_FILES["file"]["tmp_name"]);
    $image = new Imagery($filename, $file);
    $image->postImage();
}
$tweet = new tweet($content, $user->id);
$tweet->post();