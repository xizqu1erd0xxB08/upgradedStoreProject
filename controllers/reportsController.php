<?php 
session_start();

// Evitar caché para el botón de atrás
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../config.php';
require_once '../models/Database.php';
require_once '../models/Sale.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: ../views/loginView.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$database = new Database($host_name, $host_admin, $host_admin_password, $database_name);

$conn = $database->getConnection();

if (!$conn) {
    die("Conexión fallida a la base de datos: " . mysqli_connect_error());
}

$sale_object = new Sale($conn);

// Ganancias totales
$total_revenue_result = $sale_object->getTotalRevenue($user_id);

if (!$total_revenue_result['success']) {
    $total_revenue = null;
    $total_revenue_error = $total_revenue_result['errorMessage'];
} else {
    $total_revenue = $total_revenue_result['total_revenue'];
}

// Productos más vendidos
$best_selling_result = $sale_object->bestSellingProducts($user_id);

if (!$best_selling_result['success']) {
    $best_selling = null;
    $best_selling_error = $best_selling_result['errorMessage'];
} else {
    $best_selling = $best_selling_result['best_selling_products'];
}

// Productos menos vendidos
$worst_selling_result = $sale_object->worstSellingProducts($user_id);

if (!$worst_selling_result['success']) {
    $worst_selling = null;
    $worst_selling_error = $worst_selling_result['errorMessage'];
} else {
    $worst_selling = $worst_selling_result['worst_selling_products'];
}


// Cerrar conexión
$database->closeConnection();

// Incluir la vista
require_once '../views/reportsView.php';
?>