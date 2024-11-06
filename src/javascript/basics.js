const baseUrl = new URL("https://localhost/twitter-clone");

function changeSize(element) {
    element.style.height = "auto";
    element.style.height = element.scrollHeight + "px";
}

for (textarea of document.getElementsByClassName("post-content-input")) {
    textarea.addEventListener("input", (a) => { changeSize(a.target) });
}
