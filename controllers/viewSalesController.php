<?php 
session_start();

// Evitar caché para el botón de atrás
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../config.php';
require_once '../models/Database.php';
require_once '../models/Sale.php';

// Validar sesión
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: ../views/loginView.php");
    exit();    
}

// Obtener la id de sesión del usuario
$user_id = $_SESSION['user_id'];

// Crear el objeto $database
$database = new Database($host_name, $host_admin, $host_admin_password, $database_name);

// Obtener la conexión
$connection = $database->getConnection();

// Validar conexión
if (!$connection) {
    die("Conexión fallida a la base de datos: " . mysqli_connect_error());
}

// Crear el objeto $sale
$sale_object = new Sale($connection);

// Obtener el método getSalesByUser
$get_sales = $sale_object->getSalesByUser($user_id);

// Validar ejecución correcta del método
if (!$get_sales['success']) {
    $database->closeConnection(); // El controller obtiene la conexión, el controller la cierra
    die($get_sales['errorMessage']);
}

// Todo correcto, obtener array de ventas
$database->closeConnection();
$array_sales = $get_sales['sales'];

require_once '../views/viewSalesView.php'; // Incluir la vista
?>