<?php
$host = "localhost";

$username = "root";
$password = "";


$GLOBALS["database"] = new mysqli($host, $username);
$GLOBALS["database"]->select_db("twitter_clone");

if ($GLOBALS["database"]->connect_error) {
    echo "Failed to connect";
    exit();
}

function build_error(string $message)
{
    die(json_encode(array(
        "error" => $message,
    )));
}


function create_user(string $username, string $password)
{
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

    $query->bind_param("sss", $username, $password_hash, $link);

    try {
        $query->execute();
        login_user($username, $password);
    } catch (mysqli_sql_exception $e) {
        build_error($e->getMessage());
    }
}

function login_user(string $username, string $password)
{
    $password_hash = hash("sha256", $password);
    $connection = $GLOBALS["database"];

    $query = $connection->prepare("SELECT `id`, `password` FROM `users` WHERE username LIKE (?)");
    $query->bind_param("s", $username);
    $query->execute();

    $query->free_result();
    $result = $query->get_result();
    if (!$result) {
        build_error("No result found");
    }

    $user = $query->fetch();
    if (!$user) {
        build_error("User not found");
    }

    $id = $user[0];
    $password = $user[1];

    if ($password != $password_hash) {
        build_error("Wrong password");
    }

    die(json_encode(array(
        "session_token" => create_user_session($id)
    )));
}

function create_user_session(int $userid): string
{
    $token = substr(base64_encode(random_bytes(50)), 0, 32);
    $connection = $GLOBALS["database"];
    $sql = sprintf("INSERT INTO `sessions` (`token`, `id`) VALUES ('%s', %d)", $token, $userid);
    $connection->query($sql);

    return $token;
}

function logout_user(string $token)
{
    $connection = $GLOBALS["database"];
    $sql = sprintf("DELETE FROM `sessions` WHERE `sessions`.`token` = '%s'", $token);
    $connection->query($sql);
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

    $sql = sprintf("SELECT `id` FROM `sessions` WHERE token LIKE '%s'", $token);
    $result = $connection->query($sql);

    $row = $result->fetch_row();
    if (empty($row)) {
        return null;
    }
    $id = $row[0];

    $sql = sprintf("SELECT `id`, `username`, `reg_date`, `profile_img` FROM `users` WHERE id LIKE %d", $id);
    $result = $connection->query($sql);

    $user = $result->fetch_row();
    if (!$user) {
        return null;
    }

    $object = new User();
    $object->token = $token;
    $object->id = $user[0];
    $object->username = $user[1];
    $object->reg_date = $user[2];
    $object->profile_image = $user[3];

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
