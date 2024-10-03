<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles/basic.css">
    <title>Document</title>
</head>

<body>

    <div id="login-container" style="display: flex; flex-direction: column">
        <p>username</p>
        <input type="user" id="username">
        <p>passsword</p>
        <input type="password" id="password">
        <button type="submit" id="SignUp">Sign Up</button>
        <button id="Login">Login</button>


        <script>
            const sign_up_button = document.getElementById("SignUp");
            const login_button = document.getElementById("Login");

            const username = document.getElementById("username");
            const password = document.getElementById("password");

            async function handle_response(response) {
                console.log(response.body);
            }

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
    </div>
</body>

</body>

</html>