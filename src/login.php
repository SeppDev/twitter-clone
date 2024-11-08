<?php
require "modules/database.php";
// gets user data
$user = getUserSession();
// checks if user is logged in, and transfers to login page if not
if ($user) {
    header("Location: ./home");
    die();
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="styles/basic.css">
    <link rel="stylesheet" type="text/css" href="styles/login.css">
    <link rel="stylesheet" type="text/css" href="styles/button.css">
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <title>Chirpify</title>
</head>

<body>

    <div id="main">

        <div id="containers">
            <div id="logo-container" class="container">
                <img id="logo" src="images/voggel.png">
            </div>
            <div id="login-container" class="container">
                <h1 style="font-size: 3.7rem; font-weight: 700">Happening now</h1>
                <label style="font-size: 2rem">Join today.</label>

                <form id="logins">
                    <div id="input-container">
                        <p>Username</p>
                        <input type="user" class="input-field" id="username">
                        <p>Passsword</p>
                        <input type="password" class="input-field" id="password">
                    </div>

                    <button class="action primary-button" id="create-account">
                        Create account
                    </button>

                    <div id="or">
                        <hr />
                        <span>or</span>
                        <hr />
                    </div>
                    <button class="action secondary-button" id="sign-in">
                        Sign in
                    </button>

                    <script>
                        const container = document.getElementById("logins");
                        const sign_up_button = document.getElementById("create-account");
                        const login_button = document.getElementById("sign-in");

                        const username = document.getElementById("username");
                        const password = document.getElementById("password");

                        // gets login tokens and uses them as cookie
                        async function handle_response(response) {
                            const json = await response.json();
                            if (json.error) {
                                alert(json.error);
                                return
                            }
                            const token = json.session_token;

                            document.cookie = `session_token=${token}; SameSite=Lax; Secure`
                            window.location.href = "./home"
                        }
                        // prevents the page from automatically reloading
                        function submit(event) {
                            event.preventDefault();
                        }
                        container.onsubmit = submit

                        // sends request to write to database
                        login_button.onclick = async () => {
                            const response = await fetch("api/login", {
                                method: "POST",
                                headers: {
                                    Username: username.value,
                                    Password: password.value
                                }
                            });
                            await handle_response(response);
                        }

                        sign_up_button.onclick = async () => {
                            const response = await fetch("api/create_account", {
                                method: "POST",
                                headers: {
                                    Username: username.value,
                                    Password: password.value
                                }
                            });
                            await handle_response(response);
                        }
                    </script>
                </form>
            </div>
        </div>



        <div id="links">
            <a>About</a>
            <a>Download the Chirpify app</a>
            <a>Help Center</a>
            <a>Terms of Service</a>
            <a>Privacy Policy</a>
            <a>Cookie Policy</a>
            <a>Accessibility</a>
            <a>Ads info</a>
            <a>Blog</a>
            <a>Careers</a>
            <a>Brand Resources</a>
            <a>Advertising</a>
            <a>Marketing</a>
            <a>X for Busniness</a>
            <a>Developers</a>
            <a>Directory</a>
            <a>Settings</a>
            <a>© 2069 Chirpify Corp.</a>
        </div>
    </div>
</body>

</html>