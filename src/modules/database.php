<?php
try {
    $GLOBALS["database"] = new PDO("mysql:host=localhost;dbname=twitter_clone", "root", "");
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}


function build_error(string $message)
{
    die(json_encode(array(
        "error" => $message,
    )));
}


function sanitize_username(string $username): string
{
    $new = filter_var($username, FILTER_SANITIZE_SPECIAL_CHARS);
    if ($new == $username) {
        return $username;
    }
    build_error("Special characters aren't allowed!");
}

function create_user(string $username, string $password)
{

    $username = sanitize_username($username);
    if (strlen($username) > 20) {
        build_error("Username is too long!");
    }

    $username = isset($username) ? $username : null;
    $password = isset($password) ? $password : null;

    if (!($username && $password)) {
        build_error("Failed to provide a username or password");
    }

    $password_hash = hash("sha256", $password);
    $connection = $GLOBALS["database"];

    // $sql = sprintf("INSERT INTO `users`(`id`, `username`, `password`, `reg_date`, `profile_img`) VALUES (NULL, :username, \"%s\", NULL, \"LINK\")", $username, $password_hash);
    $query = $connection->prepare("INSERT INTO `users`(`id`, `username`, `password`, `reg_date`, `profile_img`) VALUES (NULL, ?, ?, NULL, ?)");

    $link = "https://static.vecteezy.com/system/resources/previews/022/461/234/large_2x/cute-tiny-cat-ai-generative-image-for-mobile-wallpaper-free-photo.jpg";

    // $query->bindParam("sss", $username, $password_hash, $link);
    $query->bindParam(1, $username, PDO::PARAM_STR);
    $query->bindParam(2, $password_hash, PDO::PARAM_STR);
    $query->bindParam(3, $link, PDO::PARAM_STR);

    try {
        $query->execute();
        login_user($username, $password);
    } catch (PDOException $e) {
        build_error($e->getMessage());
    }
}

function login_user(string $username, string $password)
{
    $username = sanitize_username($username);
    $password_hash = hash("sha256", $password);
    $connection = $GLOBALS["database"];

    $query = $connection->prepare("SELECT `id`, `password` FROM `users` WHERE username LIKE (?)");
    $query->bindParam(1, $username, PDO::PARAM_STR);

    try {
        $query->execute();
    } catch (PDOException $e) {
        build_error($e->getMessage());
    }

    $result = $query->fetch(PDO::FETCH_ASSOC);
    if (empty($result)) {
        build_error("No user found");
    }

    $found_id = $result["id"];
    $found_password = $result["password"];

    if ($found_password != $password_hash) {
        build_error("Wrong password");
    }

    $query->closeCursor();
    die(json_encode(array(
        "session_token" => create_user_session($found_id)
    )));
}

function create_user_session(int $userid): string
{
    $token = substr(base64_encode(random_bytes(50)), 0, 32);
    $connection = $GLOBALS["database"];
    $query = $connection->prepare("INSERT INTO `sessions` (`token`, `id`) VALUES (?, ?)");
    $query->bindParam(1, $token, PDO::PARAM_STR);
    $query->bindParam(2, $userid, PDO::PARAM_INT);

    $query->execute();

    return $token;
}

function logout_user(string $token)
{
    $connection = $GLOBALS["database"];
    $query = $connection->prepare("DELETE FROM `sessions` WHERE `token` = ?");
    $query->bindParam(1, $token, PDO::PARAM_STR);
    try {
        $query->execute();
    } catch (PDOException $e) {
        build_error($e->getMessage());
    }
}

class User
{
    public string $token;
    public int $id;
    public string $username;
    public string $profile_image;
    public string $reg_date;

}

function get_user_session(): User|null
{
    $connection = $GLOBALS["database"];

    $token = isset($_COOKIE["session_token"]) ? $_COOKIE["session_token"] : null;
    if (empty($token)) {
        return null;
    }

    $query = $connection->prepare("SELECT `id` FROM `sessions` WHERE token LIKE ?");
    $query->bindParam(1, $token, PDO::PARAM_STR);
    $result = $query->execute();
    if ($result === false) {
        return null;
    }

    $row = $query->fetch(PDO::FETCH_ASSOC);
    if (empty($row)) {
        return null;
    }

    $query = $connection->prepare("SELECT `id`, `username`, `reg_date`, `profile_img` FROM `users` WHERE id LIKE ?");
    $query->bindParam(1, $row["id"], PDO::PARAM_INT);
    $query->execute();

    $row = $query->fetch(PDO::FETCH_ASSOC);
    if (empty($row)) {
        return null;
    }

    $object = new User();
    $object->token = $token;
    $object->id = $row["id"];
    $object->username = $row["username"];
    $object->reg_date = $row["reg_date"];
    $object->profile_image = $row["profile_img"];

    return $object;
}

function sql($expression)
{
    $query = $GLOBALS["database"]->query($expression);
    return $query->fetch_all(PDO::FETCH_ASSOC);
}
function post()
{
    return sql("SELECT * FROM posts");
}
function author($result)
{
    return sql("SELECT * FROM users WHERE id LIKE " . $result['author']);
}

function loadPosts()
{
    $result = post();
    foreach ($result as $post) {
        $result1 = author($post);
        echo "<div class=\"tweet\">
    <div class=\"profile\">
        <div class=\"pfp\"><img src=" . $result1[0]['profile_img'] . " width=\"60px\" height=\"60px\" alt=\"\"></div>
        <div class=\"name\">" . $result1[0]['username'] . "</div>
    </div>
    <div class=\"\content\">" . $post['content'] . "
    </div>
    </div>";
    }
}
