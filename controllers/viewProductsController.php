<?php 
session_start();

// Evitar caché para el botón de atrás
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../config.php';
require_once '../models/Database.php';
require_once '../models/Product.php';

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

// Crear el objeto $product
$product_object = new Product($connection);

// Obtener el método getProductsByUser
$get_products = $product_object->getProductsByUser($user_id);

// Validar ejecución correcta del método
if (!$get_products['success']) {
    // Retornar response array de error:
    $database->closeConnection();
    die($get_products['errorMessage']);
}

// Todo correcto, obtener array de productos
$database->closeConnection();
$array_products = $get_products['products'];

// Incluir la vista
require_once '../views/viewProductsView.php';
?>