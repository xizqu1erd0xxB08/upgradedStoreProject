<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Evitar caché para el botón de atrás
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Validar sesión
if (!isset($_SESSION['userId']) || !isset($_SESSION['userName'])) {
    header("Location: loginView.php");
    exit();
}

// Esta vista normalmente se carga desde formUpdateProductController.php.
// El controlador crea $productById; este respaldo evita "undefined variable"
// en VS Code y deja campos vacíos si alguien abre la vista directamente.
$productById = $productById ?? [
    'product_id' => '',
    'product_name' => '',
    'product_price' => '',
    'current_stock' => ''
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar producto</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h3>Editar producto</h3>
    <form action="../controllers/updateProductController.php" method="post">
        <label for="productName">Nombre del producto:</label>
        <input type="text" name="productName" value="<?php echo $productById['product_name']; ?>">
        <br><br>

        <label for="productPrice">Precio del producto:</label>
        <input type="number" name="productPrice" value="<?php echo $productById['product_price']; ?>">
        <br><br>

        <label for="currentStock">Stock del producto:</label>
        <input type="number" name="currentStock" value="<?php echo $productById['current_stock']; ?>">
        <br><br>
        <input type="hidden" name="productId" value="<?php echo $productById['product_id']; ?>">

        <button type="submit">Editar</button>
    </form>
</body>
</html>
