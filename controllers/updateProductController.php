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

$user_id = $_SESSION['user_id']; // Variable necesaria 

// Validar que los datos del formulario pre-llenado hayan llegado correctamente mediante el método POST
if (!isset($_POST['product_name']) || !isset($_POST['product_price']) || !isset($_POST['current_stock']) || !isset($_POST['product_id'])) {
    die("Datos del formulario para editar producto no encontrados");
}

// Definir variables necesarias para llevar a cabo el método
$product_name = $_POST['product_name'];
$product_price = $_POST['product_price'];
$current_stock = $_POST['current_stock'];
$product_id = $_POST['product_id'];

$database = new Database($host_name, $host_admin, $host_admin_password, $database_name); // Crear objeto database

$connection = $database->getConnection(); // Obtener conexión

// Verificar conexión
if (!$connection) {
    die("Conexión fallida a la base de datos:");
}

$product = new Product($connection); // Crear objeto $product

$update_product = $product->updateProduct($product_name, $product_price, $current_stock, $user_id, $product_id); // Obtener método updateProduct

// Verificar que el método se haya ejecutado correctamente
if (!$update_product['success']) {
    $database->closeConnection();
    die($update_product['errorMessage']);
}

// Éxito
$database->closeConnection();
header("Location: viewProductsController.php");
exit();
?>