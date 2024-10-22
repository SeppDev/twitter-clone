<?php
require "modules/database.php";
$user = getUserSession();

if (!$user) {
    header("Location: ./login");
    die();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Chirpify</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="styles/basic.css">
    <link rel="stylesheet" href="styles/home.css">

    <script src="javascript/basics.js"></script>
    <script src="javascript/logout.js"></script>
    <script defer src="javascript/post.js"></script>
    <script defer src="javascript/posts.js"></script>
</head>

<body>
    <dialog id="post-dialog">
        <div>
            <label>post</label>
        </div>
    </dialog>
    <div class="containers">
        <div class="actions">
            <button class="action" onclick="openPostDialog()">Post</button>
            <button class="action" onclick="logout()">Logout</button>
        </div>
        <main id="posts">
            <?php
            fetchTweets(null);
            ?>
        </main>
    </div>
</body>

</html>