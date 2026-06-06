<?php 
session_start();  // Iniciar sesión para acceder a $_SESSION
 
// Evitar que la página sea almacenada en el caché del navegador (para el botón de atrás)
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Validar que el usuario esté logueado
if(!isset($_SESSION['userId']) || !isset($_SESSION['userName']) || !isset($_SESSION['rolesArray'])){
    header("Location: loginView.php");  // Si no está logueado, redirigir al login
    exit();
}

// Guardar datos de sesión en variables para facilitar su uso
$userName = $_SESSION['userName'];
$rolesArray = $_SESSION['rolesArray']; // Array con los role_id del usuario [1, 2] o [1] o [2]
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
    <h1>Bienvenido, <?php echo $userName; ?>!</h1>

    <h3>Tus roles son:</h3>
    <ul>
        <!-- Forma 2 de foreach (con : endforeach) porque estamos mezclando HTML -->
        <?php foreach($rolesArray as $roleId): ?>
            <li>
                <?php 
                    // Convertir role_id numérico a texto legible
                    if($roleId === 1){echo "Administrador";}
                    if($roleId === 2){echo "Dueño de tienda/negocio/local";}
                ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <hr>

    <h3>Acciones que puedes realizar:</h3>

    <!-- Mostrar link de "Create User" SOLO si el usuario tiene rol Admin (1) -->
    <?php if(in_array(1, $rolesArray)): ?>  <!-- in_array busca si 1 existe en $roles -->
        <a href="registerView.php">Crear usuario nuevo (Solo administradores)</a><br>
    <?php endif; ?>

    <!-- Links de acciones que el usuario puede realizar -->
    <a href="createProductView.php">Crear producto</a><br>
    <a href="../controllers/viewProductsController.php">Ver y administrar productos</a><br>
    <a href="../controllers/registerSaleFormController.php">Registrar nueva venta</a><br>
    <a href="../controllers/viewSalesController.php">Ver historial de ventas</a><br>
    <a href="../controllers/reportsController.php">Ver reportes de ventas</a><br>
    <a href="../controllers/logoutController.php">Cerrar sesión</a>
</body>
</html>