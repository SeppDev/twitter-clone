    <?php
require "../modules/database.php";

$user = getUserSession();

if (!$user) {
    build_error("not logged in?");
}

if (!isset($_POST["content"])) {
    build_error("no content?");
}

if (!isset($_POST["postId"])) {
    build_error("no id?");
}

if (getUserByName($_POST["username"])->id != $user->id) {
    build_error("not your post?");
}

editTweet($_POST["postId"], $_POST["content"]);