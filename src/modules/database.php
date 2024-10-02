<?php
$host = "localhost";

$username = "root";
$password = "";
// $connection = new mysqli($host, $username);

$GLOBALS["database"] = new mysqli($host, $username);

if ($GLOBALS["database"]->connect_error) {
    echo "Failed to connect";
    exit();
}

function create_user(string $username, string $password)
{
    $password_hash = hash("sha256", $password);
    
    $connection = $GLOBALS["database"];
    $connection->select_db("twitter_clone");

    // $sql = "INSERT INTO `users`(`id`, `username`, `password`, `reg_date`, `profile_img`) VALUES (" . "NULL," . $username . "," . $password_hash . ",NULL,'LINK'" . ")";
    // echo $sql;
    // $connection->query($sql);
}
