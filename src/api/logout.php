<?php
require "../modules/database.php";

$token = getUserSessionToken();
if (!$token) {
    build_error("Not logged in");
}

logoutUser($token);