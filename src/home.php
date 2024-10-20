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
    <link rel="stylesheet" href="styles/home.css">
    <script src="../javascript/posts.js"></script>
</head>

<body>
    <div id="container">
        <dialog id="dialog">
            <form class="tweetC" method="POST">
                <div class="flex">
                    <div>content</div>
                    <a onclick="closeDialog()" href="">
                        <img src="images/free-cross-icon-3203-thumb.png" width="18px" height="20px" class="cross">
                    </a>
                </div>
                <label for="content"></label><input type="text" class="input-field1" id="content">
                <div>image url</div>
                <label for="content2"></label>
                <input type="file" accept="image/*" class="input-field2" id="fileInput">
                <button id="clearFileInput">clear</button>
                <button id="submit">submit</button>
            </form>
        </dialog>
        <nav>
            <button id="logout" class="button">
                logout
            </button>
            <button id="createTweet" onclick="tweet()" class="button">
                Create tweet
            </button>
            <?php
            $user = getUserSession();
            echo "<div class=\"profile1\">";
            echo "<img src=" . $user->profile_image . "  width=\"60px\" height=\"60px\" id=\"img1\">";
            echo "<div class=\"name1\">" . $user->username . "</div>";
            echo "</div>";
            ?>
        </nav>
        <div id="wrapper1">
            <div class="aside">
                <h1>Users</h1>
                <?php
                $users = getUsers();
                foreach ($users as $user) {
                    echo "<div id=\"user_" . $user['id'] . "\" class=\"users\">";
                    echo "<img src=\"" . $user['profile_img'] . "\" width=\"100px\" height=\"100px\">";
                    echo "<p>" . $user['username'] . "</p>";
                    echo "</div>";
                }
                ?>
            </div>

            <div id="posts">
                <?php
                fetchTweets(null);
                ?>
            </div>
            <script>
                function createElementFromHTML(htmlString) {
                    var div = document.createElement('div');
                    div.innerHTML = htmlString.trim();
                    return div.firstChild;
                }

                const logout = document.getElementById("logout");
                const dialog = document.getElementById("dialog");
                const Submit = document.getElementById("submit");
                const content = document.getElementById("content");

                const fileInput = document.getElementById("fileInput");
                const clearFileInput = document.getElementById("clearFileInput");

                let wrapper = document.getElementById("wrapper");

                function closeDialog() {
                    dialog.open = false;
                }

                logout.onclick = async () => {
                    const response = await fetch("api/logout", {
                        method: "POST",
                    })
                    if (response.error) {
                        alert(response.error)
                        return;
                    }
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

                clearFileInput.onclick = () => {
                    fileInput.value = null;
                }

                Submit.onclick = async () => {
                    Submit.innerText = "Positing...";
                    let formData = new FormData();
                    formData.append("file", fileInput.files[0]);
                    formData.append("content", content.value);
                    const response = await fetch("api/post", {
                        method: "POST",
                        body: formData
                    });
                    dialog.open = false;
                    await handle_response(response);
                    Submit.innerText = "Post";
                }
                async function handle_response(response) {
                    const content = await response.text();
                    const element = createElementFromHTML(content);
                    wrapper.appendChild(element);
                }

            </script>
        </div>
    </div>
</body>

</html>