<?php
session_start();
include "php/database.php";
global $db;
?>
<html>
<head>
    <title>Chirpify</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
<div class="wrapper">

    <?php
    $query = $db->prepare("SELECT * FROM posts");
    $query->execute();
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    for ($i = 0; $i < count($result); $i++) {
        $query = $db->prepare("SELECT * FROM users WHERE id = " . $result[$i]['author']);
        $query->execute();
        $result1 = $query->fetch(PDO::FETCH_ASSOC);
        echo "<div class=\"tweet\">
    <div class=\"profile\">
        <div class=\"pfp\"><img src=" . $result1['profile_img'] . " width=\"60px\" height=\"60px\"></div>
        <div class=\"name\">" . $result1['username'] . "</div>
    </div>
    <div class=\"\content\">" . $result[$i]['content'] . "
    </div>
    </div>";
    }
    ?>

</div>
</body>
</html>