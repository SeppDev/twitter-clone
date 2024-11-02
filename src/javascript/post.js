let postDialog = document.getElementById("post-dialog");

async function openPostDialog() {
    postDialog.open = true;
}

const postTextInput = document.getElementById("post-text-input");
const postImageInput = document.getElementById("post-image-input");
postTextInput.value = null;
postImageInput.value = null;

async function post() {
    let formData = new FormData();
    formData.append("file", postImageInput.files[0]);
    formData.append("content", postTextInput.value);
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
    } catch {}
    
    const postElement = createElementFromHTML(text);
    posts.insertBefore(postElement, posts.firstChild);
    postDialog.open = false;
}

function createElementFromHTML(htmlString) {
    let div = document.createElement('div');
    div.innerHTML = htmlString.trim();

    return div.firstChild;
}