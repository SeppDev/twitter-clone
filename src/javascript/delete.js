async function deletePost(post) {
    let username = document.getElementById(`name_${post.getAttribute("post_id")}`).innerText;
    let postId = post.getAttribute("post_id");
    let formData = new FormData();
    let parent = post.parentElement;
    formData.append("username", username);
    formData.append("post_id", postId);

    const response = await fetch(`${baseUrl}/api/delete`, {
        method: "POST",
        body: formData
    });
    const json = await response.json();
    if (json.error) {
        alert(json.error);
        return;
    }
    parent.removeChild(post);
    alert(json.success);
}