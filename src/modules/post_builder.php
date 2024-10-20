<?php

function buildPost(int $authorId, string $username, string $content, int $postId, bool $likeStatus, int $likeCount) {
    $component = $GLOBALS["post_component"];

    $component = str_replace("{{base_url}}", "https://localhost/twitter-clone/", $component);

    $component = str_replace("{{author_id}}", "$authorId", $component);
    $component = str_replace("{{username}}", $username, $component);
    $component = str_replace("{{content}}", $content, $component);
    $component = str_replace("{{post_id}}", $postId, $component);
    $component = str_replace("{{like_count}}", $likeCount, $component);
    $component = str_replace("{{like_status}}", $likeStatus ? "true" : "false", $component);

    return $component;
}
