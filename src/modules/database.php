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
    return $username;
    // return $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
}

function createUser(string $username, string $password)
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

    $query = $connection->prepare("INSERT INTO `users`(`id`, `username`, `password`, `reg_date`, `profile_img`) VALUES (NULL, ?, ?, NULL, ?)");

    $link = "https://static.vecteezy.com/system/resources/previews/022/461/234/large_2x/cute-tiny-cat-ai-generative-image-for-mobile-wallpaper-free-photo.jpg";

    $query->bindParam(1, $username, PDO::PARAM_STR);
    $query->bindParam(2, $password_hash, PDO::PARAM_STR);
    $query->bindParam(3, $link, PDO::PARAM_STR);
    try {
        $query->execute();
        loginUser($username, $password);
    } catch (PDOException $e) {
        build_error($e->getMessage());
    }
}

function loginUser(string $username, string $password)
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
        "session_token" => createUserSession($found_id)
    )));
}

function createUserSession(int $userid): string
{
    $token = substr(base64_encode(random_bytes(50)), 0, 32);
    $connection = $GLOBALS["database"];
    $query = $connection->prepare("INSERT INTO `sessions` (`token`, `id`) VALUES (?, ?)");
    $query->bindParam(1, $token, PDO::PARAM_STR);
    $query->bindParam(2, $userid, PDO::PARAM_STR);
    $query->execute();

    return $token;
}

function logoutUser(string $token)
{
    $connection = $GLOBALS["database"];
    $query = $connection->prepare("DELETE FROM `sessions` WHERE `sessions`.`token` LIKE ?");
    $query->bindParam(1, $token, PDO::PARAM_STR);
    try {
        $query->execute();
    } catch (PDOException $e) {
        build_error($e->getMessage());
    }
}

class User
{
    public int $id;
    public string $username;
    public string $profile_image;
    public string $reg_date;

}

function getUserById(int $id): User|null
{
    $connection = $GLOBALS["database"];
    $query = $connection->prepare("SELECT `id`, `username`, `reg_date`, `profile_img` FROM `users` WHERE id LIKE (?)");
    $query->bindParam(1, $id, PDO::PARAM_INT);
    $query->execute();

    $user = $query->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        return null;
    }

    $object = new User();
    $object->id = $user['id'];
    $object->username = $user['username'];
    $object->reg_date = $user['reg_date'];
    $object->profile_image = $user['profile_img'];

    return $object;
}

function verifySessionToken(string $token): int|null {
    $connection = $GLOBALS["database"];
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

    return $row["id"];
}

function getUserSessionToken(): string|null {
    $token = isset($_COOKIE["session_token"]) ? $_COOKIE["session_token"] : null;
    if (empty($token)) {
        return null;
    }

    $id = verifySessionToken($token);
    if (empty($id)) {
        return null;
    }

    return $token;
}

function getUserSession(): User|null
{
    $token = getUserSessionToken();
    if (empty($token)) {
        return null;
    }

    $id = verifySessionToken($token);
    if (empty($id)) {
        return null;
    }

    return getUserById($id);
}

function likeStatus(int $postId, int $userId): bool
{
    $connection = $GLOBALS["database"];
    $query = $connection->prepare("SELECT * FROM `likes` WHERE author LIKE ? AND link LIKE ?");
    $query->bindParam(1, $userId, PDO::PARAM_INT);
    $query->bindParam(2, $postId, PDO::PARAM_INT);
    $query->execute();

    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return true;
    }
    return false;
}

function postLikes(int $postId): int {
    $connection = $GLOBALS["database"];
    $query = $connection->prepare("SELECT COUNT(*) FROM `likes` WHERE link LIKE ?");
    $query->bindParam(1, $postId, PDO::PARAM_INT);
    $query->execute();

    $result = $query->fetch(PDO::FETCH_ASSOC);
    return $result["COUNT(*)"];
}

function like(int $postId, User $user): bool
{
    $connection = $GLOBALS["database"];
    $result = likeStatus($postId, $user->id);

    if ($result) {
        $query = $connection->prepare("DELETE FROM `likes` WHERE (`author`) LIKE ? AND link LIKE ? ");
        $query->bindParam(1, $user->id, PDO::PARAM_INT);
        $query->bindParam(2, $postId, PDO::PARAM_INT);
        $query->execute();
        $result = false;
    } else {
        $query = $connection->prepare("INSERT INTO `likes` (`author`, `link`) VALUES (?, ?)");
        $query->bindParam(1, $user->id, PDO::PARAM_INT);
        $query->bindParam(2, $postId, PDO::PARAM_INT);
        $query->execute();
        $result = true;
    }
    $count = postLikes($postId);
    die(json_encode(array(
        "result" => $result,
        "count" => $count,
    )));
}

class tweet
{
    private int $authorId;
    private string $content;
    function __construct($content, $authorId)
    {
        $this->content = $content;
        $this->authorId = $authorId;
    }
    private function posts()
    {
        $connection = $GLOBALS["database"];
        $query = $connection->prepare("SELECT * FROM posts");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    public function post(): void
    {
            $connection = $GLOBALS["database"];
            $query = $connection->prepare("SELECT * FROM `images` ORDER BY id DESC LIMIT 1");
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $query = $connection->prepare("INSERT INTO `posts` (`content`, `author`, `image`) VALUES (?, ?, ?)");
            $query->bindParam(1, $this->content, PDO::PARAM_STR);
            $query->bindParam(2, $this->authorId, PDO::PARAM_INT);
            $query->bindParam(3, $result['id'], PDO::PARAM_INT);
            $query->execute();

            $postId = $connection->lastInsertId();

            $user = getUserById($this->authorId);
            $likeStatus = likeStatus($postId, $this->authorId);

            $component = file_get_contents("../components/tweet.html");
            echo buildTweet($component, $user->profile_image, $user->username, $this->content, $postId, $likeStatus, postLikes($postId), $result['id']);
            die();
    }
    private function author($result)
    {
        $connection = $GLOBALS["database"];
        $query = $connection->prepare("SELECT * FROM users WHERE id LIKE ?");
        $query->bindParam(1, $result['author'], PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    public function loadAllPosts()
    {
        $result = $this->posts();
        if (!$result) {
            echo "no posts";
        }

        $component = file_get_contents("components/tweet.html");

        foreach ($result as $post) {
            $author = $this->author($post);

            $profile_image = $author["profile_img"];
            $username = $author["username"];
            $content = $post["content"];
            $id = $post["id"];
            $likeStatus = likeStatus($id, $this->authorId);
            $image = $post["image"];
            echo buildTweet($component, $profile_image, $username, $content, $id, $likeStatus, postLikes($id), $image);
        }
    }
}

function buildTweet(string $component, string $profile_image, string $username, string $content, int $postId, bool $status, string $likeCount, int $imageId)
{
    $component = str_replace("{{profile_image}}", $profile_image, $component);
    $component = str_replace("{{username}}", $username, $component);
    $component = str_replace("{{content}}", $content, $component);
    $component = str_replace("{{post_id}}", $postId, $component);
    $component = str_replace("{{like_count}}", $likeCount, $component);
    $component = str_replace("{{like_status}}", boolToText($status), $component);
    $component = str_replace("{{image}}", $imageId, $component);
    return $component;
}

function boolToText(bool $bool) {
    if ($bool) {
        return "true";
    }
    return "false";
}


class Imagery {
    private string $imageName;
    private string $imageData;

    function __construct(string $imageName, string $imageData){
        $this->imageName = $imageName;
        $this->imageData = $imageData;
    }

    public function postImage(): void  {
        $connection = $GLOBALS["database"];
        $query = $connection->prepare("INSERT INTO `images` (`image_name`, `data`) VALUES (?, ?)");
        $query->bindParam(1, $this->imageName, PDO::PARAM_STR);
        $query->bindParam(2, $this->imageData, PDO::PARAM_LOB);
        $query->execute();
    }
}

function getImage($id)
{
    $connection = $GLOBALS["database"];
    $query = $connection->prepare("SELECT * FROM `images` where id = ?");
    $query->bindParam(1, $id, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    return $result['data'];
    die();
}