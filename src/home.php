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
    <button id="createTweet">
        Create tweet
    </button>
    <dialog class="dialog">
        <form class="tweetC">

        </form>
    </dialog>

    <script>
        const logout = document.getElementById("logout");
        const createTweet = document.getElementById("createTweet");
        const dialog = document.getElementsByClassName("dialog");
        logout.onclick = async () => {
            const response = await fetch("api/logout", {
                method: "POST",
            })
            document.cookie = "session_token="
            window.location.reload();
        }
        createTweet.onclick = dialog.open = true;
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