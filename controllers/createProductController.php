<?php
session_start();

// Evitar caché para el botón de atrás
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../config.php';
require_once '../models/Database.php';
require_once '../models/Product.php';

// Validar que el usuario este logueado
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: ../views/loginView.php");
    exit();    
}

// Validar que los datos hayan llegado por POST
if (!isset($_POST['product_name']) || !isset($_POST['product_price']) || !isset($_POST['current_stock'])) {
    die("Datos del producto no encontrados");
}

// Guardar datos del producto en variables
$product_name = $_POST['product_name'];
$product_price = $_POST['product_price'];
$current_stock = $_POST['current_stock'];

// Crear objeto Database y obtener conexion
$database = new Database($host_name, $host_admin, $host_admin_password, $database_name);
$connection = $database->getConnection();

// Validar que la conexion a la base de datos haya sido exitosa
if (!$connection) {
    die("Conexion a la base de datos fallida: " . mysqli_connect_error());
}

// Crear objeto Product
$product = new Product($connection);

// Llamar al método createProduct
$create_product = $product->createProduct($product_name, $product_price, $current_stock, $_SESSION['user_id']);

// Validar que el producto haya sido creado correctamente
if (!$create_product['success']) { 
    $database->closeConnection(); // Cerrar conexión antes de morir para no dejar conexiones abiertas en caso de error
    die("Error: " . $create_product['errorMessage']); // Mostrar mensaje de error específico que nos dio el método createProduct
}

// Éxito, cerrar conexión y redirigir a dashboard
$database->closeConnection();
header("Location: ../views/dashboard.php");
exit();
?>