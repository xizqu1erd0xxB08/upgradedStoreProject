<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h2>Iniciar sesión</h2>
    <!-- Mostrar el mensaje de prohibición al usuario para acceder a un formulario protegido -->
    <?php if (isset($_SESSION['flashError'])): ?>
        <p style="color: red;"> <?php echo $_SESSION['flashError']; unset($_SESSION['flashError']); ?></p>
    <?php endif; ?>
    <form action="../controllers/loginController.php" method="post" class="login-form">
        <label for="userIdentifier">Nombre de usuario o Correo electrónico:</label>
        <input type="text" id="userIdentifier" name="userIdentifier" required>
        <br><br>

        <label for="userPassword">Contraseña:</label>
        <input type="password" id="userPassword" name="userPassword" required>
        <br><br>

        <button type="submit">Iniciar sesión</button>
        <br><br>
    </form>

        <!-- Mejorar loginView.php para que usuarios que no tengan cuenta accedan al link de registro. -->
         <p>¿No tienes cuenta? <a href="publicRegisterView.php">Regístrate</a></p> 
</body>
</html>