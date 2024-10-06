<?php
require "../modules/database.php";
$headers = getallheaders();

$content = isset($headers['content']) ? $headers['content'] : null;
$object = get_user_session();
if (!$content) {
    echo "ERROR";
}
$object1 = new tweet($content, $object->id);
$object1->post();
$object1->loadPosts();