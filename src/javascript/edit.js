const editDialog = document.getElementById("edit-dialog");
const editTextInput = document.getElementById("edit-text-input");
const editImageInput = document.getElementById("edit-image-input");
let username;
let postElement;

function openEditDialog(post, content) {
    editTextInput.value = content;
    username = document.getElementById(`name_${post.getAttribute("post_id")}`).innerText;
    postElement = post;
    editDialog.open = true;
}


async function edit() {
    let formData = new FormData();
    formData.append("content", editTextInput.value);
    formData.append("postId", postElement.getAttribute("post_id"));
    formData.append("image", editImageInput.files[0]);
    editDialog.open = false;
    
    const response = await fetch(`${baseUrl}/api/edit`, {
        method: "POST",
        body: formData
    });
    
    const text = await response.text();
    try {
        const json = JSON.parse(text);
        if (json.error) {
            alert(json.error);
            return;
        }
    } catch { }
    
    const postImage = postElement.getElementsByClassName("post_media")[0];
    const content = postElement.getElementsByClassName("post-content")[0];
    content.innerText = editTextInput.value;
    postImage.src += "#1";
    editImageInput.value = "";
    editTextInput.value = "";
}

function clearEdit() {
    editImageInput.value = null;
}