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
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles/basic.css">
    <link rel="stylesheet" href="styles/home.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script defer src="javascript/posts.js"></script>
    <title>Chirpify</title>
</head>

<body>
    <div class="containers">
        <!-- <div class="actions"></div> -->
        <main id="content">
            <div id="post_dialog">
                <div id="post_dialog_inner">

                </div>
                <img class="profile_picture" src=<?php echo "'api/get_profile_image?userid=$user->id'" ?>>
                <div id="post_dialog_container">
                    <input type="text" id="post_dialog_text">
                </div>
            </div>
            <div id="posts">
                <?php
                    fetchTweets(null);
                ?>
            </div>
        </main>
    </div>
</body>

</html>