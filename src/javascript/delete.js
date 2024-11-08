//deletes the post given as parameter. ran be used for both posts and replies
async function deletePost(post) {
    let username = document.getElementById(`name_${post.getAttribute("post_id")}`).innerText;
    let postId = post.getAttribute("post_id");
    let formData = new FormData();
    let parent = post.parentElement;
    formData.append("username", username);
    formData.append("post_id", postId);
    //uses form data to send write requests
    const response = await fetch(`${baseUrl}/api/delete`, {
        method: "POST",
        body: formData
    });
    //awaits response from php
    const json = await response.json();
    // throws error if function returns build error
    if (json.error) {
        alert(json.error);
        return;
    }
    //removes element
    parent.removeChild(post);
    alert(json.success);
}