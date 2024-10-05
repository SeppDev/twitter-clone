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
    <button id="createTweet" onclick="tweet()">
        Create tweet
    </button>
    <dialog id="dialog">
        <form class="tweetC">
            content
            <label for="content"></label><input type="text" class="input-field1" id="content">

        </form>
    </dialog>

    <script>
        const logout = document.getElementById("logout");
        const createTweet = document.getElementById("createTweet");
        const dialog = document.getElementById("dialog");
        logout.onclick = async () => {
            const response = await fetch("api/logout", {
                method: "POST",
            })
            document.cookie = "session_token="
            window.location.reload();
        }
        function tweet() {
            dialog.open = true;
        }
    </script>
    <div class="wrapper">
    
    <?php
        $user = get_user_session();
        if (!$user) {
            header("Location: ./login");
            die();
        }
        loadPosts();
        ?>

    </div>
</body>

</html>