<?php 
session_start();

// Evitar caché para el botón de atrás
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../config.php';
require_once '../models/Database.php';
require_once '../models/Sale.php';

// Validar sesión
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: ../views/loginView.php");
    exit();    
}

// Obtener la id de sesión del usuario
$user_id = $_SESSION['user_id'];

// Validar que llegó sale_id por POST
if (!isset($_POST['sale_id'])) {
    die("ID de la venta no encontrada");
}

$sale_id = $_POST['sale_id']; // Crear variable para ejecutar el método

$database = new Database($host_name, $host_admin, $host_admin_password, $database_name); // Crear el objeto $database
 
$connection = $database->getConnection(); // Obtener la conexión

// Validar conexión
if (!$connection) {
    die("Conexión fallida a la base de datos: " . mysqli_connect_error());
}

// Crear el objeto $sale
$sale_object = new Sale($connection);

// Obtener el método getSaleDetails
$get_sale_details = $sale_object->getSaleDetails($sale_id);

// Validar ejecución correcta del método
if (!$get_sale_details['success']) {
    $database->closeConnection(); // El controller obtiene la conexión, el controller la cierra
    die($get_sale_details['errorMessage']);
}

// Todo correcto, extraer array de detalles
$database->closeConnection();
$array_sale_details = $get_sale_details['sale_details'];

require_once '../views/viewSaleDetailsView.php'; // Incluir la vista
?>