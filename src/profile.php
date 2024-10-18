<?php
require "modules/database.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../styles/profile.css">
    <link rel="stylesheet" type="text/css" href="../styles/basic.css">
    <title>Documtent</title>
</head>

<body>
    <div id="page">
        <div id="profilepage">
            <div id="profile">
                <div class="banner">
                    <?php
                    $username = $_GET["username"];
                    $user = getUserByName($username);
                    if (empty($user)) {
                        die("User not found");
                    }

                    echo "<img class='img-banner' src='../api/get_profile_image?userid=$user->id'>";
                    ?>


                    <button onclick="yes()" id="profile-image-container">
                        <div id="edit-profile-image">
                            <?php
                            echo "<img class='profile-image' src='../api/get_profile_image?userid=$user->id'>";
                            ?>
                        </div>
                    </button>

                    <script>
                        async function yes() {
                            const pickerOpts = {
                                types: [
                                    {
                                        description: "Images",
                                        accept: {
                                            "image/*": [".png", ".jpeg", ".jpg"],
                                        },
                                    },
                                ],
                                excludeAcceptAllOption: true,
                                multiple: false,
                            };
                            const files = await window.showOpenFilePicker(pickerOpts);
                            console.log(files[0]);
                        }
                    </script>
                </div>
                <div class="profile-information">
                    <div>
                        <h2>
                            <?php
                            echo $user->username;
                            ?>
                        </h2>
                        <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Esse, veritatis sapiente deleniti
                            iusto
                            pariatur animi asperiores, tenetur quo laborum, quae consequatur praesentium? Quisquam odio
                            sapiente quas laboriosam quae nisi eaque!</p>
                    </div>
                </div>
                <nav id="profile-navigation">
                    <button class="navigation-button">Posts</button>
                    <button class="navigation-button">Liked</button>
                </nav>
            </div>
            <div id="profile-posts">
                <?php
                    fetchTweets($user->id);
                ?>
            </div>
        </div>
    </div>
</body>

</html>