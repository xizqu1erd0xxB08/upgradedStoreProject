<?php 
session_start();

// Evitar caché para el botón de atrás
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../config.php';
require_once '../models/Database.php';
require_once '../models/User.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']) || !in_array(1, $_SESSION['roles'])) {
    header("Location: ../views/loginView.php");
    exit();
}
// Validar que el usuario sea admin (paso a implementar después)

// Validar que los datos del formulario hayan llegado correctamente
if(!isset($_POST['user_name']) || !isset($_POST['password']) || !isset($_POST['confirm_password']) || !isset($_POST['roles'])){
    header("Location: ../views/registerView.php");
    exit();
}

// Crear las variables necesarias
$user_name = $_POST['user_name'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$roles_array = array_map('intval', $_POST['roles']);

// Crear objeto $dabatase
$database = new Database($host_name, $host_admin, $host_admin_password, $database_name);

// Obtener la conexión con el método getConnection() del objeto $database
$connection = $database->getConnection();

// Verificar que la conexión haya sido exitosa
if(!$connection){
    die("Error de conexión a la base de datos. " . mysqli_connect_error());
}

// Crear objeto $user
$user = new User($connection);

// Realizar el registro de usuario con el método signUp() del objeto $user
$signUp = $user->signUp($user_name, $password, $confirm_password, $roles_array);

// Verificar que el registro de usuario haya sido exitoso
if(!$signUp['success']){
    $database->closeConnection();
    die("Error: " . $signUp['errorMessage']);
}

// Éxito, redirigir a registerView.php
$database->closeConnection();
header("Location: ../views/registerView.php");
exit();
?>