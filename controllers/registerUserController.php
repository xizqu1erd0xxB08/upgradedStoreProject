<?php 
// REFACTORIZAR CONTROLLER A PDO 
session_start();

// Evitar caché del navegador (botón atrás)
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once dirname(__DIR__, 1) . '/config.php';
require_once dirname(__DIR__, 1) . '/models/Database.php';
require_once dirname(__DIR__, 1) . '/models/User.php';

// Verificar que solo administradores (role_id = 1) puedan registrar nuevos usuarios
if (!isset($_SESSION['userId']) || !isset($_SESSION['userName']) || !in_array(1, $_SESSION['rolesArray'])) {
    // Implementar 'flash message' para mostrarle al usuario la causa de su prohibición de entrar al formulario
    /* Un 'flash message' guarda un mensaje en la sesión; la vista destino lee ese mensaje para mostrarlo al 
    usuario, y luego lo elimina de la sesión para que apareza una sola vez */
    $_SESSION['flashError'] = 'No tienes permiso para acceder a esta sección';
    header("Location: ../views/loginView.php");
    exit();
}

// Validar que los datos del formulario hayan llegado por POST
if (!isset($_POST['userName']) || !isset($_POST['userEmail']) || !isset($_POST['userPassword']) || 
    !isset($_POST['confirmPassword']) || !isset($_POST['rolesArray'])) {
    header("Location: ../views/registerView.php");
    exit();
}

// Crear las variables necesarias
$userName = $_POST['userName'];
$userEmail = $_POST['userEmail'];
$userPassword = $_POST['userPassword'];
$confirmPassword = $_POST['confirmPassword'];
/* 'array_map()' aplica una función a todo un arreglo automáticamente, 'intval' convierte textos 
numéricos en enteros reales. El objetivo es transformar un array de strings en un array de números
enteros limpios y seguros, evitando escribir un buble foreach() */
$rolesArray = array_map('intval', $_POST['rolesArray']);

// Crear la conexión
$database = new Database($hostName, $hostAdmin, $hostAdminPassword, $databaseName);
$connection = $database->getConnection();

// Verificar que la conexión haya sido exitosa
if ($connection === null) {
    die("No se pudo conectar a la base de datos: " . $database->getConnectionError());
}

// Crear objeto $user
$user = new User($connection);

// Llamar al método signUp y guardar su resultado
$signUpResult = $user->signUp($userName, $userEmail, $userPassword, $confirmPassword, $rolesArray);

// Verificar que el registro de usuario haya sido realizado correctamente
if (!$signUpResult['success']) {
    $database->closeConnection();
    die("Error: " . $signUpResult['errorMessage']);
}

// Éxito, cerrar conexión y redirigir a registerView.php
$database->closeConnection();
header("Location: ../views/registerView.php");
exit();
?>