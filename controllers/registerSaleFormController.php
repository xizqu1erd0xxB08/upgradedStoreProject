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

// Crear la conexión
$database = new Database($hostName, $hostAdmin, $hostAdminPassword, $databaseName);
$connection = $database->getConnection();

// Ahora, con PDO, si la conexión falló el método 'getConnection()' retorna 'null'
if ($connection === null) {
    $database->closeConnection();
    die("No se pudo conectar a la base de datos: " . $database->getConnectionError());
}

// Crear objeto $product
$product = new Product($connection);  

// Obtener todos los productos de un usuario
$getProductsByUser = $product->getProductsByUser($userId); 

// Validar que se hayan obtenido todos los productos del usuario exitosamente
if (!$getProductsByUser['success']) {
    die($getProductsByUser['errorMessage']);
}

// Extraer array de productos
$allProducts = $getProductsByUser['allProducts'];

$database->closeConnection();

// Incluir la vista 
require_once '../views/registerSaleFormView.php';
?>