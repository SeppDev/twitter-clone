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

    <script defer src="javascript/logout.js"></script>
    <script defer src="javascript/basics.js"></script>
    <script defer src="javascript/post.js"></script>
    <script defer src="javascript/posts.js"></script>
    <script defer src="javascript/edit.js"></script>
    <script defer src="javascript/delete.js"></script>
</head>

<body>

    <dialog id="post-dialog">
        <div>
            <textarea placeholder="I like fortnite" class="post-content-input" id="post-text-input"></textarea>

            <div class="post-options">
                <div>
                    <input type="file" accept="image/*" id="post-image-input">
                </div>
                <button class="action primary-button" onclick="post()">Post</button>
            </div>
        </div>
    </dialog>
    <dialog id="edit-dialog">
        <div>
            <textarea placeholder="I like blending cats" class="post-content-input" id="edit-text-input"></textarea>

            <div class="post-options">
                <div>
                    <button class="action secondary-button" onclick="clearEdit()">Clear</button>
                    <input type="file" accept="image/*" id="edit-image-input">
                </div>
                <button class="action primary-button" onclick="edit()">Submit</button>
            </div>

        </div>
    </dialog>

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