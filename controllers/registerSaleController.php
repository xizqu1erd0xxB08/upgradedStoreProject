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


// Loop que revise los 5 pares y solo agregue los que tienen product_id NO vacío y tienen quantity > 0
$products_array = [];

for ($i=1; $i <= 5 ; $i++) { 
    $product_id = $_POST["product_id_$i"]; 
    $quantity = (int)$_POST["quantity_$i"];

    // Ignorar si el dropdown está vacío o quantity es 0
    if (empty($product_id) || $quantity <= 0) {
        continue; // Salta a la siguiente iteración
    }

    // Agregar producto válido al array
    $products_array[] = [
        'product_id' => (int)$product_id,
        'quantity' => $quantity
    ];
}

// Validar que al menos un producto fue seleccionado
if (empty($products_array)) {
    die("Debe seleccionar al menos un producto con cantidad mayor a 0");
}

$database = new Database($host_name, $host_admin, $host_admin_password, $database_name); // Crear objeto $database

$connection = $database->getConnection(); // Obtener conexión a la base de datos

// Verificar conexión exitosa a la base de datos
if (!$connection) {
    die("Conexión fallida a la base de datos." . mysqli_connect_error());
}

$sale = new Sale($connection); // Crear objeto $sale

$products_sale = $sale->registerSale($user_id, $products_array); // LLamar al método que registra la venta

// Validar éxito del método
if (!$products_sale['success']) {
    $database->closeConnection();
    die($products_sale['errorMessage']);
}

// éxito
$database->closeConnection();
header("Location: ../views/dashboard.php"); // ¿si debería redirigir aquí, o a dónde? creo que aún no tenemos clara la estructura
?>