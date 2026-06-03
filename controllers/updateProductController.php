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

// Validar que los datos del formulario pre-llenado hayan llegado correctamente mediante el método POST
if (!isset($_POST['productName']) || !isset($_POST['productPrice']) || !isset($_POST['currentStock']) || 
!isset($_POST['productId'])) {
    die("Datos del formulario para editar producto no encontrados");
}

// Definir variables necesarias para llevar a cabo el método
$productName = $_POST['productName'];
$productPrice = $_POST['productPrice'];
$currentStock = $_POST['currentStock'];
$productId = $_POST['productId'];

// Crear la conexión
$database = new Database($hostName, $hostAdmin, $hostAdminPassword, $databaseName);
$connection = $database->getConnection();

// Ahora, con PDO, si la conexión falló el método 'getConnection()' retorna 'null'
if ($connection === null) {
    die("No se pudo conectar a la base de datos: " . $database->getConnectionError());
}

// Crear el objeto $product
$product = new Product($connection);

$updateProduct = $product->updateProduct($productName, $productPrice, $currentStock, $userId, $productId);

// Verificar que el método se haya ejecutado correctamente
if (!$updateProduct['success']) {
    $database->closeConnection();
    die($updateProduct['errorMessage']);
}

// Éxito, cerrar conexión y redirigir a la vista de productos
$database->closeConnection();
header("Location: viewProductsController.php");
exit();
?>