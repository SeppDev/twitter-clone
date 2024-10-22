const postDialog = document.getElementById("post-dialog");
const postImageInput = document.getElementById("post-image");
postImageInput.value = null;

async function openPostDialog() {
    postDialog.open = true;
}