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

function sanitize_username(string $username): string
{
    return $username;
    // return $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
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

    $sql = sprintf("INSERT INTO `users`(`id`, `username`, `password`, `reg_date`, `profile_img`) VALUES (NULL, \"%s\", \"%s\", NULL, \"LINK\")", $username, $password_hash);

    try {
        $connection->query($sql);
        login_user($username, $password);
    } catch (mysqli_sql_exception $e) {
        build_error($e->getMessage());
    }
}

function login_user(string $username, string $password)
{
    $username = sanitize_username($username);
    $password_hash = hash("sha256", $password);
    $connection = $GLOBALS["database"];

    $sql = sprintf("SELECT `id`, `password` FROM `users` WHERE username LIKE '%s'", $username);
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

class tweet {
    private $id;
    private $content;
    function __construct($content, $id) {
        $this->content = $content;
        $this->id = $id;
    }
    private function sql($expression)
    {
        $query = $GLOBALS["database"]->query($expression);
        return $query->fetch_all(PDO::FETCH_ASSOC);
    }
    private function posts()
    {
        return $this->sql("SELECT * FROM posts");
    }
    public function post(): void
    {
        $this->sql(sprintf("INSERT INTO `posts` (`content`, `author`) VALUES ('%s', %d)", $this->content, $this->id));
    }
    private function author($result)
    {
        return $this->sql("SELECT * FROM users WHERE id LIKE " . $result[1]);
    }
    public function loadPosts(): void
    {
        $result = $this->posts();
        foreach ($result as $post) {
            $result1 = $this->author($post);
            echo "<div class=\"tweet\">
    <div class=\"profile\">
        <div class=\"pfp\"><img src=" . $result1[0][4] . " width=\"60px\" height=\"60px\" alt=\"\"></div>
        <div class=\"name\">" . $result1[0][1] . "</div>
    </div>
    <div class=\"\content\">" . $post[2] . "
    </div>
    </div>";
        }
    }
}







