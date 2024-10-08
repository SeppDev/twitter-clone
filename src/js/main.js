const logout = document.getElementById("logout");
let dialog = document.getElementById("dialog");
let Submit = document.getElementById("submit");
let content = document.getElementById("content");
let wrapper = document.getElementById("wrapper");
let createTweet = document.getElementById("createTweet")
console.log(wrapper);
logout.onclick = async () => {
    const response = await fetch("api/logout", {
        method: "POST",
    })
    document.cookie = "session_token=";
    window.location.reload();
}

function tweet() {
    dialog.open = true;
}
function submit(event) {
    event.preventDefault();
}
container.onsubmit = submit
Submit.onclick = async () => {
    const response = await fetch("api/post", {
        method: "POST",
        headers: {
            content: content.value
        }
    });
    dialog.open = false;
    await handle_response(response);
}
async function handle_response(response) {
    let json = await response.json();
    const posts = json.posts;
    wrapper.innerHTML = "";
    console.log(posts);
    for (let i = 0; i<posts.length; i++) {
       wrapper.innerHTML += posts[i];
    }
}