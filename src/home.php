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
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">

    <script src="javascript/basics.js"></script>
    <script src="javascript/logout.js"></script>
    <script defer src="javascript/post.js"></script>
    <script defer src="javascript/posts.js"></script>
    <script defer src="javascript/edit.js"></script>
    <script defer src="javascript/delete.js"></script>
</head>

<body>
    <dialog id="post-dialog">
        <div>
            <input type="text" placeholder="I like fortnite!" id="post-text-input">
            <input type="file" accept="image/*" id="post-image-input">

            <button class="action primary-button" onclick="post()">Post</button>
        </div>
    </dialog>
    <dialog id="reply-dialog">
        <div>
            <textarea placeholder="I like splattering brains!" class="post-content-input" id="post-text-reply-input"></textarea>

            <div class="post-options">
                <div>
                    <input type="file" accept="image/*" id="post-image-reply-input">
                </div>
                <button class="action primary-button" id="reply-button" onclick="post(selectedElement)">Post</button>
            </div>
        </div>
    </dialog>

    <?php
    echo readRelativeFile("/components/edit_dialog.html");

    ?>

    <div id="containers">
        <div id="actions">
            <button class="action primary-button" onclick="openPostDialog()">Post</button>
            <button class="action primary-button" onclick="logout()">Logout</button>
        </div>
        <main id="posts">
            <?php
            fetchTweets(null);
            ?>
        </main>
    </div>
</body>

</html>