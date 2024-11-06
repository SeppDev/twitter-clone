
function checkPostStatus(likeButton, status, likesCountLabel, likes) {
    const svg = likeButton.children[1];
    likesCountLabel.innerText = likes
    svg.className.baseVal = status && "post_heart_liked" || "post_heart_unliked";
}

async function likePost(likeButton, postId, currentStatus, likesCountlabel, likes) {
    checkPostStatus(likeButton, currentStatus == false, likesCountlabel, likes);

    const response = await fetch(`${baseUrl}/api/like`, {
        method: "POST",
        headers: {
            postId: postId,
            newStatus: currentStatus == false,
        },
    });

    const json = await response.json();
    if (json.error) {
        alert(json.error);

        checkPostStatus(likeButton, currentStatus, likesCountlabel, likes);
        return currentStatus;
    }

    checkPostStatus(likeButton, json.result, likesCountlabel, json.count);

    return json.result;
}

function handlePost(post) {
    const likeButton = post.getElementsByClassName("post_like_button")[0];
    const likesCountLabel = post.getElementsByClassName("post_likes")[0];
    const editButton = post.getElementsByClassName("post_edit")[0];
    const postContent =  post.getElementsByClassName("post-content")[0].innerText;
    const buttons = post.getElementsByClassName("buttons")[0];
    const postDelete = buttons.getElementsByClassName("post_delete")[0];

    const postId = post.getAttribute("post_id");
    let likeStatus = post.getAttribute("status") == "true";
    let likes = parseInt(likesCountLabel.innerText);

    checkPostStatus(likeButton, likeStatus, likesCountLabel, likes);

    let awaitingResponse = false;
    likeButton.onclick = async () => {
        if (awaitingResponse == true) {
            return;
        }
        awaitingResponse = true;
        likeStatus = await likePost(likeButton, postId, likeStatus, likesCountLabel, likes);
        awaitingResponse = false;
    }
    editButton.onclick = () => {
        openEditDialog(post, postContent);
    };

    postDelete.onclick = async () => {
        await deletePost(post);
    };
}

const posts = document.getElementById("posts");
const observer = new MutationObserver((list, _) => {
    const post = list[0].addedNodes[0];
    if (!post) {
        return;
    }
    handlePost(post);
});
observer.observe(posts, { childList: true })

for (let child of posts.children) {
    handlePost(child);
}