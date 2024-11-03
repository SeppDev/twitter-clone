const baseUrl = new URL("https://localhost/twitter-clone/");

function changeSize(element) {
    console.log("Hello")
    element.style.height = "auto";
    element.style.height = element.scrollHeight + "px";
}

for (textarea of document.getElementsByClassName("post-content-input")) {
    textarea.addEventListener("input", () => { changeSize(textarea) });
}