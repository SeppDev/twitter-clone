<?php
require "../modules/database.php";

$user = get_user_session();
logout_user($user->token);