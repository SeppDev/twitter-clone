<?php
require "../modules/database.php";

$user = getUserSessionToken();
if (!$user) {
    build_error("Not logged in");
}

logoutUser($token);