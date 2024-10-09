<?php
require "../modules/database.php";

$user = getUserSession();
if (!$user) {
    build_error("Not logged in");
}

// logout_user($user->token);