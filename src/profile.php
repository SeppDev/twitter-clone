<?php
require "modules/database.php";
//gets user data
$username = $_GET["username"];
$user = getUserByName($username);
if (empty($user)) {
    header("Location: https://localhost/twitter-clone/login");
    die();
}
$currentUser = getUserSession();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../styles/profile.css">
    <link rel="stylesheet" type="text/css" href="../styles/basic.css">
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">

    <script defer src="../javascript/delete.js"></script>
    <script defer src="../javascript/edit.js"></script>
    <script defer src="../javascript/delete.js"></script>
    <script defer src="../javascript/basics.js"></script>
    <script defer src="../javascript/posts.js"></script>
    <script defer src="../javascript/post.js"></script>
    <title>Chirpify</title>
</head>

<body>
    <?php


    echo '<script defer src="../javascript/edit_profile.js"></script>';
    // checks if user is authorized to moderate these tweets
    if ($currentUser->userName == $username) {
        echo readRelativeFile("/components/edit_pfp_dialog.html");
    }
    echo readRelativeFile("/components/edit_dialog.html");
    echo readRelativeFile("/components/reply_dialog.html");

    ?>

    <div id="page">
        <div id="profilepage">
            <div id="profile">
                <div class="banner">
                    <?php
                    // gets user banner
                    echo "<img class='img-banner' src='../api/get_profile_banner_image?userid=$user->id'>";
                    ?>

                    <button id="profile-image-container">
                        <?php
                        // gets profile image
                        echo "<img class='profile-image' src='../api/get_profile_image?userid=$user->id'>";
                        ?>

                    </button>
                </div>
                <!-- gets user information -->
                <div class="profile-information">
                    <div>
                        <h2 id="profile-username">
                            <?php
                            echo $user->userName;
                            ?>
                        </h2>
                        <p id="profile-description"><?php echo $user->description ?></p>
                    </div>
                </div>
            </div>
            <div id="posts">
                <?php
                //fetches all tweets from a specific user
                fetchTweets($user->id);
                ?>
            </div>
        </div>
    </div>
</body>

</html>