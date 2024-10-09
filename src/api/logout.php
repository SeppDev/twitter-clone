<?php
require "../modules/database.php";

$user = getUserSession();
logout_user($user->token);