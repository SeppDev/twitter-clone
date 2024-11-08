<?php
// current working directory
$dir = __DIR__;
require "$dir/../modules/post_builder.php";

//reads file using path
function readRelativeFile(string $path): string
{
    $dir = __DIR__;
    $content = file_get_contents("$dir/../$path");
    if ($content == false) {
        build_error("Failed to find path: $dir/../$path");
    }

    return $content;
}
//connects to db
try {
    $GLOBALS["database"] = new PDO("mysql:host=localhost;dbname=twitter_clone", "root", "");
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
$GLOBALS["post_component"] = readRelativeFile("components/post.html");

//main error throw function
function build_error(string $message)
{
    die(json_encode(array(
        "error" => $message,
    )));
}

//creates user and writes to db
function createUser(string $userName, string $password)
{
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

    $query = $connection->prepare("INSERT INTO `users`(`id`, `username`, `password`, `reg_date`, `profile_img`) VALUES (NULL, ?, ?, NULL, NULL)");

    $query->bindParam(1, $userName, PDO::PARAM_STR);
    $query->bindParam(2, $password_hash, PDO::PARAM_STR);
    try {
        $query->execute();
        loginUser($userName, $password);
    } catch (PDOException $e) {
        build_error($e->getMessage());
    }
}

//logs user in and writes to db
function loginUser(string $userName, string $password)
{
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

//generates random string
function randomString(int $length)
{
    return substr(base64_encode(random_bytes($length + 10)), 0, $length);
}

//creates a user session and binds login token
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

//class for managing user data
class User
{
    public int $id;
    public string $userName;
    public string|null $profile_image;
    public string $reg_date;
    public string $description;

}

//get a user by their id
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

//get user by name
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

//verify session token
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

//get token of user session
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


//edits tweet. can be used for tweet or reply
function editTweet(int $postId, string $content)
{
    $connection = $GLOBALS["database"];
    $query = $connection->prepare("UPDATE posts SET content=?, image=? WHERE id LIKE ?");
    $query->bindParam(1, $content, PDO::PARAM_STR);
    if (isset($_FILES["image"])) {
        $fileTmpPath = $_FILES["image"]['tmp_name'];
        $fileData = file_get_contents($fileTmpPath);
        $query->bindParam(2, $fileData, PDO::PARAM_STR);
    } else {
        $value = null;
        $query->bindParam(2, $value);
    }
    $query->bindParam(3, $postId, PDO::PARAM_INT);
    $query->execute();

    echo json_encode(array(
        "status" => true
    ));
}

function deleteTweet(int $postId)
{
    $connection = $GLOBALS["database"];
    $query = $connection->prepare("DELETE FROM `posts` WHERE id LIKE ?");
    $query->bindParam(1, $postId, PDO::PARAM_STR);
    $query->execute();
    echo json_encode(array(
        "success" => "you got it"
    ));
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

//gets post likes
function postLikes(int $postId): int
{
    $connection = $GLOBALS["database"];
    $query = $connection->prepare("SELECT COUNT(*) FROM `likes` WHERE link LIKE ?");
    $query->bindParam(1, $postId, PDO::PARAM_INT);
    $query->execute();

    $result = $query->fetch(PDO::FETCH_ASSOC);
    return $result["COUNT(*)"];
}

function getPost(int $postId): tweet|null
{
    $connection = $GLOBALS["database"];
    $query = $connection->prepare("SELECT `author`, `content` FROM `posts` WHERE id LIKE ?");
    $query->bindParam(1, $postId, PDO::PARAM_INT);
    $query->execute();

    $result = $query->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        return null;
    }
    return new tweet($result["content"], $result["author"]);
}

//tweet class. manages tweets and stores data
class tweet
{
    public int $authorId;
    public string $content;
    function __construct($content, $authorId)
    {
        $this->content = $content;
        $this->authorId = $authorId;
    }
    // can be used for post or reply
    public function post(int|null $postId): void
    {
        $connection = $GLOBALS["database"];
        if ($postId) {
            $query = $connection->prepare("INSERT INTO `posts` (`content`, `author`, `image`, `is_reply`) VALUES (?, ?, ?, ?)");
            $query->bindParam(4, $postId, PDO::PARAM_STR);
        } else {
            $query = $connection->prepare("INSERT INTO `posts` (`content`, `author`, `image`) VALUES (?, ?, ?)");
        }
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

        echo buildPost($this->authorId, $user->userName, $this->content, $postId, false, postLikes($postId), true, "post");
    }
}
//can be used to fetch with a single user
function fetchTweets(int|null $authorId): void
{
    $connection = $GLOBALS["database"];
    $currentUser = getUserSession();

    $query = $connection->prepare("SELECT * FROM `posts` WHERE `is_reply` IS NULL ORDER BY id DESC");
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
        if ($author->userName == $currentUser->userName) {
            $authorized = true;
        } else {
            $authorized = false;
        }
        echo buildPost($author->id, $author->userName, $content, $postId, $likeStatus, postLikes($postId), $authorized, "post");
    }
}
//fetches comments
function fetchComments(int $postId)
{
    $connection = $GLOBALS["database"];
    $currentUser = getUserSession();
    $comments = "";

    $query = $connection->prepare("SELECT * FROM `posts` WHERE `is_reply` = ? ORDER BY id DESC");
    $query->bindParam(1, $postId, PDO::PARAM_INT);
    $query->execute();

    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $post) {
        $author = getUserById($post["author"]);
        $commentId = $post["id"];
        $likeStatus = likeStatus($postId, $currentUser->id);
        $content = $post["content"];

        if ($author->userName == $currentUser->userName) {
            $authorized = true;
        } else {
            $authorized = false;
        }
        $comments = $comments . buildPost($author->id, $author->userName, $content, $commentId, $likeStatus, postLikes($postId), $authorized, "comment");
    }
    return $comments;
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