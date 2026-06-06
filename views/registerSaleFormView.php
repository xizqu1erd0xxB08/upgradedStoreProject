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

// Esta vista normalmente se carga desde registerSaleFormController.php.
// El controlador crea $allProducts; este respaldo evita "undefined variable"
// en VS Code y evita errores si alguien abre la vista directamente.
$allProducts = $allProducts ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar nueva venta</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h2>Registrar nueva venta</h2>
    <form action="../controllers/registerSaleController.php" method="post">
        <!-- Proceder a crear el formulario con 5 dropdowns fijos para cada venta, es decir,
        se pueden vender máximo 5 productos en cada venta (por ahora) -->
        <!-- Producto 1 -->
         <label>Producto 1:</label>
         <select name="productId1" id="productId1">
            <option value="">-- Seleccione Producto --</option>
            <?php foreach($allProducts as $product): ?>
                <option value="<?php echo $product['product_id']; ?>">
                    <?php echo $product['product_name']; ?> (Stock disponible: <?php echo $product['current_stock']; ?>)
                </option>
            <?php endforeach; ?>
         </select>
         <label>Cantidad:</label>
         <input type="number" name="quantity1" id="quantity1" min="0" value="0">
         <br><br>
        
        <!-- Producto 2 -->
         <label>Producto 2:</label>
         <select name="productId2" id="productId2">
            <option value="">-- Seleccione Producto --</option>
            <?php foreach($allProducts as $product): ?>
                <option value="<?php echo $product['product_id']; ?>">
                    <?php echo $product['product_name']; ?> (Stock disponible: <?php echo $product['current_stock']; ?>)
                </option>
            <?php endforeach; ?>
         </select>
         <label>Cantidad:</label>
         <input type="number" name="quantity2" id="quantity2" min="0" value="0">
         <br><br>

        <!-- Producto 3 -->
         <label>Producto 3:</label>
         <select name="productId3" id="productId3">
            <option value="">-- Seleccione Producto --</option>
            <?php foreach($allProducts as $product): ?>
                <option value="<?php echo $product['product_id']; ?>">
                    <?php echo $product['product_name']; ?> (Stock disponible: <?php echo $product['current_stock']; ?>)
                </option>
            <?php endforeach; ?>
         </select>
         <label>Cantidad:</label>
         <input type="number" name="quantity3" id="quantity3" min="0" value="0">
         <br><br>

        <!-- Producto 4 -->
         <label>Producto 4:</label>
         <select name="productId4" id="productId4">
            <option value="">-- Seleccione Producto --</option>
            <?php foreach($allProducts as $product): ?>
                <option value="<?php echo $product['product_id']; ?>">
                    <?php echo $product['product_name']; ?> (Stock disponible: <?php echo $product['current_stock']; ?>)
                </option>
            <?php endforeach; ?>
         </select>
         <label>Cantidad:</label>
         <input type="number" name="quantity4" id="quantity4" min="0" value="0">
         <br><br>

        <!-- Producto 5 -->
         <label>Producto 5:</label>
         <select name="productId5" id="productId5">
            <option value="">-- Seleccione Producto --</option>
            <?php foreach($allProducts as $product): ?>
                <option value="<?php echo $product['product_id']; ?>">
                    <?php echo $product['product_name']; ?> (Stock disponible: <?php echo $product['current_stock']; ?>)
                </option>
            <?php endforeach; ?>
         </select>
         <label>Cantidad:</label>
         <input type="number" name="quantity5" id="quantity5" min="0" value="0">
         <br><br>

        <button type="submit">Registrar venta</button>

    </form>

</body>
</html>
