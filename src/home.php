<?php
require "modules/database.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Chirpify</title>
    <link rel="stylesheet" href="styles/style.css">
</head>

<body>
    <div class="wrapper">
    
    <?php
        $user = get_user_session();
        if ($user) {
            echo $user->username;
        } else {
            echo "No user found";
        }
        // loadPosts();
        ?>

    </div>
</body>

</html>