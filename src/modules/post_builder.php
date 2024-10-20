<?php

function buildPost(int $authorId, string $username, string $content, int $postId, bool $hasImage, bool $likeStatus, int $likeCount) {
    $component = $GLOBALS["post_component"];

    $component = str_replace("{{profile_image}}", "api/get_profile_image?userid=$authorId", $component);
    $component = str_replace("{{username}}", $username, $component);
    $component = str_replace("{{content}}", $content, $component);
    $component = str_replace("{{post_id}}", $postId, $component);
    $component = str_replace("{{like_count}}", $likeCount, $component);
    $component = str_replace("{{like_status}}", boolToText($likeStatus), $component);

    return $component;
}

// function buildTweet(string $username, string $content, int $postId, bool $status, int $likeCount, bool $hasImage, int $authorId): string
// {
//     $component = $GLOBALS["component"];

//     if ($hasImage) {
//         $component = str_replace("{{image}}", "<img src=\"api/get_image?file={{post_id}}\" class=\"image\">", $component);
//     } else {
//         $component = str_replace("{{image}}", "", $component);
//     }

//     $component = str_replace("{{profile_image}}", "api/get_profile_image?userid=$authorId", $component);

//     $component = str_replace("{{username}}", $username, $component);
//     $component = str_replace("{{content}}", $content, $component);
//     $component = str_replace("{{post_id}}", $postId, $component);
//     $component = str_replace("{{like_count}}", $likeCount, $component);
//     $component = str_replace("{{like_status}}", boolToText($status), $component);
//     return $component;
// }