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
require_once dirname(__DIR__, 1) . '/models/Sale.php';

// Validar sesión del usuario (si no tiene la sesión activa, redirigir a la vista del log in)
if (!isset($_SESSION['userId']) || !isset($_SESSION['userName']) || !isset($_SESSION['rolesArray'])) {
    header("Location: ../views/loginView.php");
    exit();    
}

// Obtener la id de sesión del usuario
$userId = $_SESSION['userId'];


// Loop que revise los 5 pares y solo agregue los que tienen productId NO vacío y tienen quantity > 0
$productsArray = [];

for ($i=1; $i <= 5 ; $i++) { 
    $productId = $_POST["productId$i"] ?? ''; // Si no existe, string vacía
    $quantity = (int)($_POST["quantity$i"] ?? 0); // Si no existe, 0

    // Ignorar si el dropdown está vacío o quantity es 0
    if (empty($productId) || $quantity <= 0) {
        continue; // Salta a la siguiente iteración
    }

    // Agregar producto válido al array
    $productsArray[] = [
        'productId' => (int)$productId,
        'quantity' => (int)$quantity
    ];
}

// Validar que al menos un producto fue seleccionado
if (empty($productsArray)) {
    die("Debe seleccionar al menos un producto con cantidad mayor a 0");
}

// Crear la conexión
$database = new Database($hostName, $hostAdmin, $hostAdminPassword, $databaseName);
$connection = $database->getConnection();

// Ahora, con PDO, si la conexión falló el método 'getConnection()' retorna 'null'
if ($connection === null) {
    die("No se pudo conectar a la base de datos: " . $database->getConnectionError());
}

// Crear objeto $sale
$sale = new Sale($connection);

// LLamar al método que registra la venta
$productsSale = $sale->registerSale($userId, $productsArray); 

// Validar éxito del método
if (!$productsSale['success']) {
    $database->closeConnection();
    die($productsSale['errorMessage']);
}

// éxito
$database->closeConnection();
header("Location: ../views/dashboard.php");
exit();
?>