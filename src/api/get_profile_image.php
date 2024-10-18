<?php
require "../modules/database.php";

$userId = $_GET["userid"];

echo getProfileImage($userId);
