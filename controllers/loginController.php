<?php 
session_start();
require_once '../config.php';
require_once '../models/Database.php';
require_once '../models/User.php';

// Validar POST de user_name y password
if(!isset($_POST['user_name']) || !isset($_POST['password'])){
    die("Datos del usuario no encontrados");
}
$user_name = $_POST['user_name'];
$password = $_POST['password'];

// Crear objeto $database
$database = new Database($host_name, $host_admin, $host_admin_password, $database_name);

// Obtener conexión
$connection = $database->getConnection();

// Validar que la conexión a la base de datos haya sido exitosa
if(!$connection){
    die("Conexión a la base de datos fallida: " . mysqli_connect_error());
}

// Crear objeto $user
$user = new User($connection);

// Llamar al método login
$login_user = $user->logIn($user_name, $password);

// Verificar que el login haya sido realizado correctamente
if(!$login_user['success']){
    die("Error: " . $login_user['errorMessage']);
}

// Si todo salió correctamente, guardar las sesiones con los datos necesarios
$_SESSION['user_id'] = $login_user['user']['user_id'];
$_SESSION['user_name'] = $login_user['user']['user_name'];
$_SESSION['roles'] = $login_user['user']['roles'];

// Cerra conexión
$database->closeConnection();

// Redirigir a dashboard (todos los roles por ahora)
header("Location: ../views/dashboard.php");
exit();
?>