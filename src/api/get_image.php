<?php
require "../modules/database.php";

$postId = $_GET["file"];

echo getPostImage($postId);
