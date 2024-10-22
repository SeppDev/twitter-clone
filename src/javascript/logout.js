async function logout() {
    document.cookie = "session_token=; expires= SameSite=Lax; Secure";
    await fetch(`${baseUrl}/api/logout`);
    document.location = baseUrl + "login";
}