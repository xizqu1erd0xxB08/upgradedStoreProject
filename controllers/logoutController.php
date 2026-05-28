<?php 
session_start(); // Para poder destruir la sesión, debemos iniciarla primero

// Borrar variables específicas de la sesión
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['roles']);

// Destruir toda la sesión
session_destroy();

// Redirigir al login
header("Location: ../views/loginView.php");
exit();
?>