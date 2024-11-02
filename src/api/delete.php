<?php
require "../modules/database.php";

$user = getUserSession();

if (!isset($_POST['username'])) {
    build_error("not logged in?");
}

if (!isset($_POST['post_id'])) {
    build_error("tf are you doing?");
}

if ($_POST['username'] != $user->userName) {
    build_error("not your post?");
}

deleteTweet($_POST['post_id']);