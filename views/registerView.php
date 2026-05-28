<?php
session_start();

// Evitar caché para el botón de atrás
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Validar sesión y rol de administrador (rol id 1)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']) || !in_array(1, $_SESSION['roles'])) {
    header("Location: loginView.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register New User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h2>Register New User (This form is only for admins)</h2>

    <form action="../controllers/registerUserController.php" method="post">

        <label for="user_name">Username:</label>
        <input type="text" id="user_name" name="user_name" required>
        <br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br><br>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <br><br>

        <label>Assign Roles:</label><br>
        <input type="checkbox" id="role_admin" name="roles[]" value="1">
        <label for="role_admin">Admin</label>
        <br>
        <input type="checkbox" id="role_shop_owner" name="roles[]" value="2">
        <label for="role_shop_owner">Shop Owner</label>
        <br><br>

        <button type="submit">Create User</button>
    </form>
</body>
</html>