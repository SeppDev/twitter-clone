<?php
session_start();
include "modules/database.php";
global $db;
?>
<html lang="">
<head>
    <title>Chirpify</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
<div class="wrapper">

    <?php
    loadPosts();
    ?>

</div>
</body>
</html>
