<?php
require "../modules/database.php";

$user = getUserSession();

if (!$user) {
    build_error("not logged in?");
}

$username = $user->userName;

header("Location: https://localhost/twitter-clone/profile/$username");
die();