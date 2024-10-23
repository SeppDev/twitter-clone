<?php

$dir = __DIR__;
require "$dir/../modules/post_builder.php";

function readRelativeFile(string $path): string
{
    $dir = __DIR__;
    $content = file_get_contents("$dir/../$path");
    if ($content == false) {
        build_error("Failed to find path: $dir/../$path");
    }

    return $content;
}

try {
    $GLOBALS["database"] = new PDO("mysql:host=localhost;dbname=twitter_clone", "root", "");
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
$GLOBALS["post_component"] = readRelativeFile("components/post.html");

function build_error(string $message)
{
    die(json_encode(array(
        "error" => $message,
    )));
}

function sanitize_username(string $userName): string
{
    return $userName;
    // return $userName = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
}

function createUser(string $userName, string $password)
{
    $userName = sanitize_username($userName);
    if (strlen($userName) > 20) {
        build_error("Username is too long!");
    }

    $userName = isset($userName) ? $userName : null;
    $password = isset($password) ? $password : null;

    if (!($userName && $password)) {
        build_error("Failed to provide a username or password");
    }

    $password_hash = hash("sha256", $password);
    $connection = $GLOBALS["database"];

    $query = $connection->prepare("INSERT INTO `users`(`id`, `username`, `password`, `reg_date`, `profile_img`) VALUES (NULL, ?, ?, NULL, ?)");

    $link = "https://static.vecteezy.com/system/resources/previews/022/461/234/large_2x/cute-tiny-cat-ai-generative-image-for-mobile-wallpaper-free-photo.jpg";

    $query->bindParam(1, $userName, PDO::PARAM_STR);
    $query->bindParam(2, $password_hash, PDO::PARAM_STR);
    $query->bindParam(3, $link, PDO::PARAM_STR);
    try {
        $query->execute();
        loginUser($userName, $password);
    } catch (PDOException $e) {
        build_error($e->getMessage());
    }
}

function loginUser(string $userName, string $password)
{
    $userName = sanitize_username($userName);
    $password_hash = hash("sha256", $password);
    $connection = $GLOBALS["database"];

    $query = $connection->prepare("SELECT `id`, `password` FROM `users` WHERE username LIKE (?)");
    $query->bindParam(1, $userName, PDO::PARAM_STR);

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

function randomString(int $length)
{
    return substr(base64_encode(random_bytes($length + 10)), 0, $length);
}

function createUserSession(int $userid): string
{
    $token = randomString(32);
    $connection = $GLOBALS["database"];
    $query = $connection->prepare("INSERT INTO `sessions` (`token`, `id`) VALUES (?, ?)");
    $query->bindParam(1, $token, PDO::PARAM_STR);
    $query->bindParam(2, $userid, PDO::PARAM_STR);
    $query->execute();

    return $token;
}


class User
{
    public int $id;
    public string $userName;
    public string|null $profile_image;
    public string $reg_date;
    public string $description;

}

function getUserById(int $id): User|null
{
    $connection = $GLOBALS["database"];
    $query = $connection->prepare("SELECT * FROM `users` WHERE id LIKE (?)");
    $query->bindParam(1, $id, PDO::PARAM_INT);
    $query->execute();

    $user = $query->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        return null;
    }

    $object = new User();
    $object->id = $user['id'];
    $object->userName = $user['username'];
    $object->reg_date = $user['reg_date'];
    $object->profile_image = $user['profile_img'];
    $object->description = $user["description"];

    return $object;
}


function getUserByName(string $userName): User|null
{
    $connection = $GLOBALS["database"];
    $query = $connection->prepare("SELECT * FROM `users` WHERE username LIKE (?)");
    $query->bindParam(1, $userName, PDO::PARAM_STR);
    $query->execute();

    $user = $query->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        return null;
    }

    $object = new User();
    $object->id = $user['id'];
    $object->userName = $user['username'];
    $object->reg_date = $user['reg_date'];
    $object->profile_image = $user['profile_img'];
    $object->description = $user["description"];

    return $object;
}

function verifySessionToken(string $token): int|null
{
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

function getUserSessionToken(): string|null
{
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
    if (isset($GLOBALS["currentUser"])) {
        return $GLOBALS["currentUser"];
    }

    $token = getUserSessionToken();
    if (empty($token)) {
        return null;
    }

    $id = verifySessionToken($token);
    if (empty($id)) {
        return null;
    }

    $user = getUserById($id);
    $GLOBALS["currentUser"] = $user;
    return $user;
}

function editTweet(int $postId, string $content)
{
    $connection = $GLOBALS["database"];
    $query = $connection->prepare("UPDATE posts SET content=? WHERE id LIKE ?");
    $query->bindParam(1, $content, PDO::PARAM_STR);
    $query->bindParam(2, $postId, PDO::PARAM_INT);
    $query->execute();
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

function postLikes(int $postId): int
{
    $connection = $GLOBALS["database"];
    $query = $connection->prepare("SELECT COUNT(*) FROM `likes` WHERE link LIKE ?");
    $query->bindParam(1, $postId, PDO::PARAM_INT);
    $query->execute();

    $result = $query->fetch(PDO::FETCH_ASSOC);
    return $result["COUNT(*)"];
}

function getUsers()
{
    $connection = $GLOBALS["database"];
    $query = $connection->prepare("SELECT * FROM `users`");
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
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
    public function post(): void
    {
        $connection = $GLOBALS["database"];
        $query = $connection->prepare("INSERT INTO `posts` (`content`, `author`, `image`) VALUES (?, ?, ?)");
        $query->bindParam(1, $this->content, PDO::PARAM_STR);
        $query->bindParam(2, $this->authorId, PDO::PARAM_INT);

        if (isset($_FILES["file"])) {
            $fileTmpPath = $_FILES["file"]['tmp_name'];
            $fileData = file_get_contents($fileTmpPath);
            $query->bindParam(3, $fileData, PDO::PARAM_STR);
        } else {
            $value = null;
            $query->bindParam(3, $value);
        }
        $query->execute();
        $postId = $connection->lastInsertId();
        $user = getUserById($this->authorId);

        echo buildPost($this->authorId, $user->userName, $this->content, $postId, false, postLikes($postId));
    }
}

function fetchTweets(int|null $authorId): void
{
    $connection = $GLOBALS["database"];
    $currentUser = getUserSession();

    $query = $connection->prepare("SELECT * FROM `posts` ORDER BY id DESC");
    if (isset($authorId)) {
        $query = $connection->prepare("SELECT * FROM `posts` WHERE author LIKE ? ORDER BY id DESC");
        $query->bindParam(1, $authorId, PDO::PARAM_INT);
    }
    $query->execute();

    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $post) {
        $author = getUserById($post["author"]);
        $postId = $post["id"];
        $likeStatus = likeStatus($postId, $currentUser->id);
        $content = $post["content"];

        echo buildPost($author->id, $author->userName, $content, $postId, $likeStatus, postLikes($postId));
    }
}
function getPostImage(int $postId): string|null
{
    $connection = $GLOBALS["database"];
    $query = $connection->prepare("SELECT `image` FROM `posts` WHERE id LIKE ?");
    $query->bindParam(1, $postId, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    header("Content-Type: image/*");
    return $result['image'];
}
