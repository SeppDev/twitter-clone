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
<div id="container">
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
            <button id="submit">submit</button>
        </form>
    </dialog>

    <script>
        const logout = document.getElementById("logout");
        let dialog = document.getElementById("dialog");
        let Submit = document.getElementById("submit");
        let content = document.getElementById("content");
        logout.onclick = async () => {
            const response = await fetch("api/logout", {
                method: "POST",
            })
            document.cookie = "session_token=";
            window.location.reload();
        }
        function tweet() {
            dialog.open = true;
        }
        function submit(event) {
            event.preventDefault();
        }
        container.onsubmit = submit
        Submit.onclick = async () => {
            const response = await fetch("api/post", {
                method: "POST",
                headers: {
                    content: content.value
                }
            });
            dialog.open = false
        }
    </script>
    <div class="wrapper">
    
    <?php
        $user = get_user_session();
        if (!$user) {
            header("Location: ./login");
            die();
        }
        $object1 = get_user_session();
        $object = new tweet("", $object1->id);
        $object->loadPosts();
        ?>

    </div>
</div>
</body>

</html>