<?php 
// REFACTORIZAR CONTROLLER A PDO 
session_start();

// Evitar caché del navegador (botón atrás)
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once dirname(__DIR__, 1) . '/config.php';
require_once dirname(__DIR__, 1) . '/models/Database.php';
require_once dirname(__DIR__, 1) . '/models/Sale.php';

// Validar sesión del usuario (si no tiene la sesión activa, redirigir a la vista del log in)
if (!isset($_SESSION['userId']) || !isset($_SESSION['userName']) || !isset($_SESSION['rolesArray'])) {
    header("Location: ../views/loginView.php");
    exit();    
}

// Obtener la id de sesión del usuario
$userId = $_SESSION['userId'];

// Validar que la variable saleId haya llegado por el método POST 
if (!isset($_POST['saleId'])) {
    die("ID de la venta NO encontrada");
}

// Crear la variable saleId
$saleId = $_POST['saleId'];

// Crear la conexión
$database = new Database($hostName, $hostAdmin, $hostAdminPassword, $databaseName);
$connection = $database->getConnection();

// Ahora, con PDO, si la conexión falló el método 'getConnection()' retorna 'null'
if ($connection === null) {
    die("No se pudo conectar a la base de datos: " . $database->getConnectionError());
}

// Crear el objeto $sale
$sale = new Sale($connection);

// Obtener el método getSaleDetails
$getSaleDetails = $sale->getSaleDetails($saleId);

// Validar ejecución correcta del método 'getSaleDetails'
if (!$getSaleDetails['success']) {
    $database->closeConnection(); // El controller obtiene la conexión, el controller la cierra
    die($getSaleDetails['errorMessage']);
}

// Éxito, cerrar conexión y extraer array de detalles de la venta
$database->closeConnection();
$arraySaleDetails = $getSaleDetails['saleDetails'];

require_once '../views/viewSaleDetailsView.php'; // Incluir la vista
?>