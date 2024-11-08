//base url for api calls
const baseUrl = new URL("https://localhost/twitter-clone/");

//opens profile page with GET method
function openProfilePage(userId) {
    window.location = `${baseUrl}/api/profile`
}

//makes the text inputs resizable
function changeSize(element) {
    element.style.height = "auto";
    element.style.height = element.scrollHeight + "px";
}
//gets the input elements and makes them resizable
for (textarea of document.getElementsByClassName("post-content-input")) {
    textarea.addEventListener("input", (a) => { changeSize(a.target) });
}