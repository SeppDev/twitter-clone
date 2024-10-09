<?php
require "../modules/database.php";

$headers = getallheaders();

$username = isset($headers['Username']) ? $headers['Username'] : null;
$password = isset($headers['Password']) ? $headers['Password'] : null;

if (!($username && $password)) {
    build_error("Failed to provide a username or password");
} 

loginUser($username, $password);
