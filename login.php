<?php require "src/modules/database.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    ?>

    <div>
        <p>username</p>
        <input type="user" id="username">
        <p>passsword</p>
        <input type="password" id="password">
        <button type="submit" id="submit">Submit</button>

        <script>
            const button = document.getElementById("submit");
            const username = document.getElementById("username");
            const password = document.getElementById("password");

            button.onclick = () => {
                fetch("src/api/accounts.php", {
                    method: "POST",
                    headers: {
                        Username: username.value,
                        Password: password.value
                    }
                });
            }

        </script>
    </div>
</body>

</body>

</html>