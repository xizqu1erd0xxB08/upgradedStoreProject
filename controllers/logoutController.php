<?php 
session_start(); // Para poder destruir la sesión, debemos iniciarla primero

// Borrar variables específicas de la sesión
unset($_SESSION['userId']);
unset($_SESSION['userName']);
unset($_SESSION['rolesArray']);

// Destruir toda la sesión
session_destroy();

// Redirigir al login
header("Location: ../views/loginView.php");
exit();
?>