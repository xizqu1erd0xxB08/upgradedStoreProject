<?php 
session_start();  // Iniciar sesión para acceder a $_SESSION
 
// Evitar que la página sea almacenada en el caché del navegador (para el botón de atrás)
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Validar que el usuario esté logueado
if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']) || !isset($_SESSION['roles'])){
    header("Location: loginView.php");  // Si no está logueado, redirigir al login
    exit();
}

// Guardar datos de sesión en variables para facilitar su uso
$user_name = $_SESSION['user_name'];
$roles = $_SESSION['roles']; // Array con los role_id del usuario [1, 2] o [1] o [2]
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <!-- Mostrar nombre del usuario -->
    <h1>Welcome, <?php echo $user_name; ?>!</h1>

    <h3>Your Roles:</h3>
    <ul>
        <!-- Forma 2 de foreach (con : endforeach) porque estamos mezclando HTML -->
        <?php foreach($roles as $role_id): ?>
            <li>
                <?php 
                    // Convertir role_id numérico a texto legible
                    if($role_id === 1){echo "Admin";}
                    if($role_id === 2){echo "Shop Owner";}
                ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <hr>

    <h3>Actions:</h3>

    <!-- Mostrar link de "Create User" SOLO si el usuario tiene rol Admin (1) -->
    <?php if(in_array(1, $roles)): ?>  <!-- in_array busca si 1 existe en $roles -->
        <a href="registerView.php">Create New User (Admin Only)</a><br>
    <?php endif; ?>

    <!-- Links pendientes de implementar (por eso usan #) -->
    <a href="createProductView.php">Create Product</a><br>
    <a href="../controllers/viewProductsController.php">Manage Products</a><br>
    <a href="../controllers/registerSaleFormController.php">Register Sale</a><br>
    <a href="../controllers/viewSalesController.php">View Sales History</a><br>
    <a href="../controllers/reportsController.php">Ver Reportes</a><br>
    <a href="../controllers/logoutController.php">Logout</a>
</body>
</html>