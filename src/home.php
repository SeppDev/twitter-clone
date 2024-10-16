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

        <div id="wrapper">

            <?php
            $user = getUserSession();
            if (!$user) {
                header("Location: ./login");
                die();
            }
            $object1 = getUserSession();
            $object = new tweet("", $object1->id);
            $object->loadAllPosts();
            ?>
        </div>
        <script>
            function createElementFromHTML(htmlString) {
                var div = document.createElement('div');
                div.innerHTML = htmlString.trim();

                // Change this to div.childNodes to support multiple top-level nodes.
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

            
            async function likePost(button, postId) {
                const response = await fetch("api/like", {
                    method: "POST",
                    headers: {
                        postId: postId
                    }
                })
                const json = await response.json()
                if (json.error) {
                    console.log(json.error);
                    return
                }
                checkLikeStatus(button, json.result == true);
                document.getElementById(`count_${postId}`).innerHTML = json.count;
            }

            function checkLikeStatus(button, status) {
                if (!status) {
                    button.style.backgroundColor = "white";
                } else {
                    button.style.backgroundColor = "red";
                }
            }
        </script>
    </div>
</body>

</html>