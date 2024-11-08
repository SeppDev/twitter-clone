//opens post dialog
async function openPostDialog() {
    const postDialog = document.getElementById("post-dialog");
    postDialog.open = true;
}

const postTextInput = document.getElementById("post-text-input");
const postImageInput = document.getElementById("post-image-input");
const replyTextInput = document.getElementById("post-text-reply-input");
const replyImageInput = document.getElementById("post-image-reply-input");
const replyDialog = document.getElementById("reply-dialog");

if (postTextInput && postImageInput) {
    postTextInput.value = null;
    postImageInput.value = null;
}

//posts either a tweet or reply
async function post(element = null) {
    let formData = new FormData();
    //checks if post or reply
    if (element) {
        formData.append("post_id", element.getAttribute("post_id"));
        formData.append("file", replyImageInput.files[0]);
        formData.append("content", replyTextInput.value);
    } else {
        formData.append("file", postImageInput.files[0]);
        formData.append("content", postTextInput.value);
    }
    const response = await fetch(`${baseUrl}/api/post`, {
        method: "POST",
        body: formData
    })

    const text = await response.text();
    try {
        const json = JSON.parse(text);
        if (json.error) {
            alert(json.error);
            return;
        }
    } catch { }
    const postDialog = document.getElementById("post-dialog");
    const postElement = createElementFromHTML(text);
    let parent;
    //checks if post or reply
    if (element) {
        parent = element.children[1];
        replyDialog.open = false;
    } else {
        parent = posts;
        postDialog.open = false;
    }
    parent.insertBefore(postElement, parent.firstChild);
    handlePost(parent.firstChild);
}
//create an element from mere text
function createElementFromHTML(htmlString) {
    let div = document.createElement('div');
    div.innerHTML = htmlString.trim();

    return div.firstChild;
}