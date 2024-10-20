function likePost(likeButton, postId) {

}

function checkPostStatus(button, status) {
    const svg = likeButton.children[1];
    svg.className.baseVal = status && "post_heart_liked" || "post_heart_unliked";
}

function handlePost(post) {
    console.log(post);
}

// const observer = new MutationObserver((list, _) => {

// });
// const posts = document.getElementById("posts");
// observer.observe(posts, { childList: true })

for (child of posts.children) {
    handlePost(child);
}