<?php
require "../modules/database.php";

$userId = isset($_GET["userid"]) ? $_GET["userid"] : readRelativeFile("../images/defaultpfp.jpeg");

echo getProfileImage($userId);
