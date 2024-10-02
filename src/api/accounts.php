<html>

<body>

    <?php
    require "../modules/database.php";

    $headers = getallheaders();

    $username = isset($headers['Username']) ? $headers['Username'] : null;
    $password = isset($headers['Password']) ? $headers['Password'] : null;

    create_user($username, $password);
    ?>

</body>

</html>