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
    <button id="logout">
        logout
    </button>

    <script>
        const logout = document.getElementById("logout");
        logout.onclick = async () => {
            const responnse = await fetch("api/logout", {
                method: "POST",
            })
            document.cookie = "session_token="
            window.location.reload();
        }
    </script>
    <div class="wrapper">
    
    <?php
        $user = get_user_session();
        if (!$user) {
            header("Location: ./login");
            die();
        } 
        echo $user->token;
        loadPosts();
        ?>

    </div>
</body>

</html>