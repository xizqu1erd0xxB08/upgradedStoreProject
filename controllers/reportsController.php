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

// Crear la conexión
$database = new Database($hostName, $hostAdmin, $hostAdminPassword, $databaseName);
$connection = $database->getConnection();

// Ahora, con PDO, si la conexión falló el método 'getConnection()' retorna 'null'
if ($connection === null) {
    die("No se pudo conectar a la base de datos: " . $database->getConnectionError());
}

// Crear el objeto $sale
$sale = new Sale($connection);

// Ganancias totales
$totalRevenueResult = $sale->getTotalRevenue($userId);

if (!$totalRevenueResult['success']) {
    $totalRevenue = null;
    $totalRevenueError = $totalRevenueResult['errorMessage'];
} else {
    $totalRevenue = $totalRevenueResult['totalRevenue'];
}

// Productos más vendidos
$bestSellingResult = $sale->bestSellingProducts($userId);

if (!$bestSellingResult['success']) {
    $bestSelling = null;
    $bestSellingError = $bestSellingResult['errorMessage'];
} else {
    $bestSelling = $bestSellingResult['bestSellingProducts'];
}

// Productos menos vendidos
$worstSellingResult = $sale->worstSellingProducts($userId);

if (!$worstSellingResult['success']) {
    $worstSelling = null;
    $worstSellingError = $worstSellingResult['errorMessage'];
} else {
    $worstSelling = $worstSellingResult['worstSellingProducts'];
}


// Cerrar conexión
$database->closeConnection();

// Incluir la vista
require_once '../views/reportsView.php';
?>