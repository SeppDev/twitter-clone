<?php
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
function author($result) {
    return sql("SELECT * FROM users WHERE id LIKE " . $result['author']);
}

function loadPosts() {
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