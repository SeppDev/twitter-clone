<?php
require "../modules/database.php";

$postId = $_GET["file"];

echo getImage($postId);
