<?php 
session_start();

// Evitar caché para el botón de atrás
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../config.php';
require_once '../models/Database.php';
require_once '../models/Product.php';
require_once '../models/Sale.php';

// Verificar sesión activa
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: ../views/loginView.php"); // Redirigir al login si la sesión no está activa
    exit();
}
$user_id = $_SESSION['user_id']; // Variable con la id del usuario

$database = new Database($host_name, $host_admin, $host_admin_password, $database_name); // Objeto database que contiene métodos útiles

$connection = $database->getConnection(); // Obtener la conexión a la base de datos

// Verificar conexión exitosa a la base de datos
if (!$connection) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}

$product = new Product($connection); // Objeto product que contiene métodos útiles

$get_products_by_user = $product->getProductsByUser($user_id); // Obtener todos los productos de un usuario

// Validar que se hayan obtenido todos los productos del usuario exitosamente
if (!$get_products_by_user['success']) {
    die($get_products_by_user['errorMessage']);
}

// Extraer array de productos
$all_products = $get_products_by_user['products'];

// Incluir la vista 
require_once '../views/registerSaleFormView.php';
?>