<?php 
session_start();

// Evitar caché para el botón de atrás
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../config.php';
require_once '../models/Database.php';
require_once '../models/Product.php';

// Validar que la sesión del usuario esté activa
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: ../views/loginView.php");
    exit();
}
$user_id = $_SESSION['user_id']; // Variable necesaria para el proceso.

// Validar que el $product_id haya llegado exitosamente por POST
if (!isset($_POST['product_id'])) {
    die("Product_id no encontrada");
}
$product_id = $_POST['product_id']; // Variable necesaria para el proceso

$database = new Database($host_name, $host_admin, $host_admin_password, $database_name); //  Objeto $database

$connection = $database->getConnection(); // Obtener la conexión mediante el método del objeto $database

// Verificar que la conexión haya sido exitosa
if (!$connection) {
    die("Conexión fallida a la base de datos: " . mysqli_connect_error());
}

$product = new Product($connection); // Objeto $product

$get_product_by_id = $product->getProductById($product_id, $user_id); // Obtener la ejecución del método necesario

// Validar que el método se haya ejecutado correctamente
if (!$get_product_by_id['success']) {
    $database->closeConnection();
    die($get_product_by_id['errorMessage']);
}

// Éxito, obtener array con los datos necesarios
$database->closeConnection();
$product_by_id = $get_product_by_id['product_by_id'];

// Llamar a la vista
require_once '../views/formUpdateProductView.php';
?>