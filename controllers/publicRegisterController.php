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

/* Controller público para registro de usuarios que permite que usuarios comunes puedan registrarse sin 
necesidad de que un usuario con rol administrador los cree */

// Validar que los datos del formulario hayan llegado por POST
if (!isset($_POST['userName']) || !isset($_POST['userEmail']) || !isset($_POST['userPassword']) || 
    !isset($_POST['confirmPassword'])) {
    header("Location: ../views/publicRegisterView.php");
    exit();
}

// Crear las variables necesarias
$userName = $_POST['userName'];
$userEmail = $_POST['userEmail'];
$userPassword = $_POST['userPassword'];
$confirmPassword = $_POST['confirmPassword'];
// Sin $rolesArray: signUp() usará el default [2] (Shop Owner)

// Crear la conexión
$database = new Database($hostName, $hostAdmin, $hostAdminPassword, $databaseName);
$connection = $database->getConnection();

// Verificar que la conexión haya sido exitosa
if ($connection === null) {
    die("No se pudo conectar a la base de datos: " . $database->getConnectionError());
}

// Crear objeto $user
$user = new User($connection);

// Llamar al método signUp y guardar su resultado sin quinto argumento para rolesArray (usa default [2])
$signUpResult = $user->signUp($userName, $userEmail, $userPassword, $confirmPassword);

// Verificar que el registro de usuario haya sido realizado correctamente
if (!$signUpResult['success']) {
    $database->closeConnection();
    die("Error: " . $signUpResult['errorMessage']);
}

// Éxito, cerrar conexión
$database->closeConnection();

// Al registrarse exitosamente, redirige al login para que inicie sesión
header("Location: ../views/loginView.php");
exit();
?>