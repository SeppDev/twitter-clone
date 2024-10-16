<?php
require "../modules/database.php";

$imgId = $_GET["file"];

echo getImage($imgId);