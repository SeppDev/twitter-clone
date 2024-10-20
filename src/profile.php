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

    <script defer src="../javascript/posts.js"></script>
    <title>Chirpify</title>
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
                        <?php
                        echo "<img class='profile-image' src='../api/get_profile_image?userid=$user->id'>";
                        ?>
                        <!-- <div id="edit-profile-image">
                            <svg xmlns="http://www.w3.org/2000/svg" id="edit-icon" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-pencil">
                                <path
                                    d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                                <path d="m15 5 4 4" />
                            </svg>
                        </div> -->


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
                        <p><?php echo $user->description ?></p>
                    </div>
                </div>
                <!-- <nav id="profile-navigation">
                    <button class="navigation-button">Posts</button>
                    <button class="navigation-button">Liked</button>
                </nav> -->
            </div>
            <div id="posts">
                <?php
                fetchTweets($user->id);
                ?>
            </div>
        </div>
    </div>
</body>

</html>