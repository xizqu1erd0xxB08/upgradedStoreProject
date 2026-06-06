<?php
session_start();

// Evitar caché para el botón de atrás
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// No se valida que el usuario sea admin porque este es un formulario público
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar nuevo usuario</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h2>Crear nueva cuenta</h2>

    <form action="../controllers/publicRegisterController.php" method="post">

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

        <button type="submit">Enviar</button>
    </form>
</body>
</html>