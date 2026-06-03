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

// Validar que la variable productId haya llegado por el método POST 
if (!isset($_POST['productId'])) {
    die("ID del producto NO encontrada");
}

// Crear la variable product_id
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

// Llamar al método deleteProduct
$deleteProduct = $product->deleteProduct($productId, $userId); 

// Verificar que se haya ejecutado correctamente
if (!$deleteProduct['success']) {
    $database->closeConnection();
    die($deleteProduct['errorMessage']);
}

// Si todo salió bien, cerrar la conexión y redirigir al controlador de la vista de productos
$database->closeConnection();
header("Location: viewProductsController.php");
exit();
?>