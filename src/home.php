<?php
require "modules/database.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Chirpify</title>
    <link rel="stylesheet" href="styles/style.css">
    <script src="js/main.js" defer>
    </script>
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


    <div id="wrapper">
    
    <?php
        $user = get_user_session();
        if (!$user) {
            header("Location: ./login");
            die();
        }
        $object1 = get_user_session();
        $object = new tweet("", $object1->id);
        $posts = $object->loadPosts();
        for ($i = 0; $i < count($posts); $i++) {
            echo $posts[$i];
        }
        ?>
    </div>

</div>
</body>

</html>