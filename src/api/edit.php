<?php
require "../modules/database.php";

$user = getUserSession();

if (!$user) {
    build_error("not logged in?");
}

if (!isset($_POST["content"])) {
    build_error("no content?");
}

if (!isset($_POST["postId"])) {
    build_error("no id?");
}

$post = getPost($_POST["postId"]);
if (!isset($post)) {
    build_error("Could not find post?");
}
if ($post->authorId != $user->id) {
    build_error("Not your post!");
}

editTweet($_POST["postId"], $_POST["content"]);