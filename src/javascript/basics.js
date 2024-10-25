const baseUrl = new URL("https://localhost/twitter-clone/");

document.querySelectorAll("textarea").forEach(function (textarea) {
    textarea.style.height = "1rem";

    textarea.addEventListener("input", function () {
        this.style.height = "auto"
        this.style.height = this.scrollHeight + "px";

        console.log("edited")
    });
});