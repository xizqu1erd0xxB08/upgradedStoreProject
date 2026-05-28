<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h2>Login</h2>
    <form action="../controllers/loginController.php" method="post" class="login-form">
        <label for="user_name">Username:</label>
        <input type="text" id="user_name" name="user_name" required>
        <br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>