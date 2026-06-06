<?php
session_start();

// Evitar caché para el botón de atrás
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Validar sesión y rol de administrador (rol id 1)
if (!isset($_SESSION['userId']) || !isset($_SESSION['userName']) || !isset($_SESSION['rolesArray']) 
    || !in_array(1, $_SESSION['rolesArray'])) {
    header("Location: loginView.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN | REGISTRAR NUEVO USUARIO</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h2>Registrar nuevo usuario (formulario solo para administradores)</h2>

    <form action="../controllers/registerUserController.php" method="post">

        <label for="userName">Nombre de usuario:</label>
        <input type="text" id="userName" name="userName" required>
        <br><br>

        <label for="userEmail">Correo electrónico:</label>
        <input type="email" id="userEmail" name="userEmail" required>
        <br><br>

        <label for="userPassword">Contraseña:</label>
        <input type="password" id="userPassword" name="userPassword" required>
        <br><br>

        <label for="confirmPassword">Confirmar contraseña:</label>
        <input type="password" id="confirmPassword" name="confirmPassword" required>
        <br><br>

        <label>Asignar Roles:</label><br>
        <input type="checkbox" id="roleAdmin" name="rolesArray[]" value="1">
        <label for="roleAdmin">Administrador</label>
        <br>
        <input type="checkbox" id="roleShopOwner" name="rolesArray[]" value="2">
        <label for="roleShopOwner">Dueño de tienda/negocio/local</label>
        <br><br>

        <button type="submit">Registrar usuario</button>
    </form>
</body>
</html>