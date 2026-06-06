<?php
// REFACTORIZAR CONTROLLER A PDO 
session_start();

// Evitar caché del navegador (botón atrás)
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once dirname(__DIR__, 1) . '/config.php';
require_once dirname(__DIR__, 1) . '/models/Database.php';
require_once dirname(__DIR__, 1) . '/models/Product.php';

// Validar sesión del usuario (si no tiene la sesión activa, redirigir a la vista del log in)
if (!isset($_SESSION['userId']) || !isset($_SESSION['userName']) || !isset($_SESSION['rolesArray'])) {
    header("Location: ../views/loginView.php");
    exit();    
}

// Obtener la id de sesión del usuario
$userId = $_SESSION['userId'];

// Validar que los datos hayan llegado por POST
if (!isset($_POST['productName']) || !isset($_POST['productPrice']) || !isset($_POST['currentStock'])) {
    die("Datos del producto no encontrados");
}

// Guardar datos del producto en variables
$productName = $_POST['productName'];
$productPrice = $_POST['productPrice'];
$currentStock = $_POST['currentStock'];

// Crear la conexión
$database = new Database($hostName, $hostAdmin, $hostAdminPassword, $databaseName);
$connection = $database->getConnection();

// Ahora, con PDO, si la conexión falló el método 'getConnection()' retorna 'null'
if ($connection === null) {
    die("No se pudo conectar a la base de datos: " . $database->getConnectionError());
}

// Crear objeto Product
$product = new Product($connection);

// Llamar al método createProduct
$createProduct = $product->createProduct($productName, $productPrice, $currentStock, $userId);

// Validar que el producto haya sido creado correctamente
if (!$createProduct['success']) { 
    $database->closeConnection(); // Cerrar conexión antes de morir para no dejar conexiones abiertas en caso de error
    die("Error: " . $createProduct['errorMessage']); // Mostrar mensaje de error específico que nos dio el método createProduct
}

// Éxito, cerrar conexión y redirigir a dashboard
$database->closeConnection();
header("Location: ../views/dashboard.php");
exit();
?>