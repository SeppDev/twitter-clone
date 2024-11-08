const editDialog = document.getElementById("edit-dialog");
const editTextInput = document.getElementById("edit-text-input");
const editImageInput = document.getElementById("edit-image-input");
let OldpostElement;

//opens edit dialog and loads default data
function openEditDialog(post, content) {
    editDialog.open = true;
    editTextInput.value = content;
    OldpostElement = post;
    changeSize(editTextInput);
}

//clears default data
function clearEdit() {
    editImageInput.value = null;
    editTextInput.value = null;
}

//changes content and image of a post or reply
async function edit() {
    let formData = new FormData();
    formData.append("content", editTextInput.value);
    formData.append("postId", OldpostElement.getAttribute("post_id"));
    formData.append("image", editImageInput.files[0]);
    //requests write to database
    const response = await fetch(`${baseUrl}/api/edit`, {
         method: "POST",
         body: formData
    });
    //awaits text response (html)
    let html = await response.text();
    //checks for error
    try {
        const json = JSON.parse(html);
        if (json.error) {
            alert(json.error);
            editDialog.open = false;
            return;
        }
    } catch { }
    //applies the changed values
    const postImage = OldpostElement.getElementsByClassName("post_media")[0];
    const content = OldpostElement.getElementsByClassName("post-content")[0];
    content.innerText = editTextInput.value;
    
    const blob = new Blob([editImageInput.files[0]], {type: "image/png"})
    postImage.src = URL.createObjectURL(blob)
    postImage.onload = () => URL.revokeObjectURL(postImage.src);
    editImageInput.value = "";
    editTextInput.value = "";
    editDialog.open = false;
}

const dialogs = document.querySelectorAll("dialog");
dialogs.forEach((dialog) => {
    const cancelButton = dialog.getElementsByClassName("dialog-close")[0];
    if (!cancelButton) {
        return;
    }

    cancelButton.onclick = () => {
        dialog.open = false;
    }
})