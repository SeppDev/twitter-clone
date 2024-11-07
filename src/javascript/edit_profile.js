const pfpInput = document.getElementById("edit-pfp-input");
const pfpBannerInput = document.getElementById("edit-pfp-banner-input");

if (pfpInput) {
    pfpInput.value = null;
    pfpBannerInput.value = null;
}

pfpInput.addEventListener("change", async () => {
    const file = pfpInput.files[0];

    let formData = new FormData();
    formData.append("image", file);

    const request = await fetch(`${baseUrl}/api/edit_pfp_image`, {
        method: "POST",
        body: formData
    })
    
    window.location.reload();
})

pfpBannerInput.addEventListener("change", async () => {
    const file = pfpBannerInput.files[0];

    let formData = new FormData();
    formData.append("image", file);

    const request = await fetch(`${baseUrl}/api/edit_pfp_banner_image`, {
        method: "POST",
        body: formData
    })

    window.location.reload();
})

const profileUsername = document.getElementById("profile-username").innerText;
const profileDescription = document.getElementById("profile-description").innerText;

const editProfileUsername = document.getElementById("edit-profile-username")
const editProfileDescription = document.getElementById("edit-profile-description")
async function editProfile() {
    let formData = new FormData();
    formData.append("username", editProfileUsername.value);
    formData.append("description", editProfileDescription.value);

    const request = await fetch(`${baseUrl}/api/edit_pfp`, {
        method: "POST",
        body: formData
    })

    openProfilePage();
}

const profileEditButton = document.getElementById("profile-image-container");
const editProfileDialog = document.getElementById("edit-profile");

profileEditButton.onclick = () => {
    editProfileUsername.value = profileUsername;
    editProfileDescription.value = profileDescription;
    editProfileDialog.open = true;
    changeSize(editProfileDescription);
}