<?php
$host = "localhost";

$username = "root";
$password = "";

$GLOBALS["sessions"] = array();



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
    $password_hash = hash("sha256", $password);
    $connection = $GLOBALS["database"];

    $sql = sprintf("INSERT INTO `users`(`id`, `username`, `password`, `reg_date`, `profile_img`) VALUES (NULL, \"%s\", \"%s\", NULL, \"LINK\")", $username, $password_hash);

    try {
        $result = $connection->query($sql);
        $user = $result->fetch_row();
        if (!$user) {
            build_error("User not found");
        }

        $id = $user[0];

        die(json_encode(array(
            "session-token" => create_user_session($id)
        )));
    } catch (mysqli_sql_exception $e) {
        build_error($e->getMessage());
    }
}

function login_user(string $username, string $password)
{
    $password_hash = hash("sha256", $password);
    $connection = $GLOBALS["database"];

    $sql = sprintf("SELECT `id`, `password` FROM `users` WHERE username LIKE \"%s\"", $username);
    $result = $connection->query($sql);

    $user = $result->fetch_row();
    if (!$user) {
        build_error("User not found");
    }

    $id = $user[0];
    $password = $user[1];

    if ($password != $password_hash) {
        build_error("Wrong password");
    }

    die(json_encode(array(
        "session-token" => create_user_session($id)
    )));
}

function create_user_session(int $userid): string
{
    $token = base64_encode(random_bytes(32));
    $tokens[$token] = $userid;

    return $token;
}

function get_user_session(string|null $token): int|null
{
    if ($token) {
        return $GLOBALS["sessions"][$token];
    }
    return null;
}

$db = new PDO('mysql:host=localhost;dbname=twitter_clone', 'root', '');
function sql($expression)
{
    global $db;
    $query = $db->query($expression);
    return $query->fetchAll(PDO::FETCH_ASSOC);
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
