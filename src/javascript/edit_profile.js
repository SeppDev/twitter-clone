const pfpInput = document.getElementById("edit-pfp-input");
pfpInput.value = null;

pfpInput.addEventListener("change", async () => {
    const file = pfpInput.files[0];

    let formData = new FormData();
    formData.append("image", file);

    const request = await fetch(`${baseUrl}/api/edit_pfp`, {
        method: "POST",
        body: formData
    })
    window.location.reload();
})

const profileEditButton = document.getElementById("profile-image-container");
const editProfileDialog = document.getElementById("edit-profile");

profileEditButton.onclick = () => {
    editProfileDialog.open = true;
}