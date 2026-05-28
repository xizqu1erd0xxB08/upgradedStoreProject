<?php
session_start();

// Evitar caché para el botón de atrás
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Validar sesión
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: loginView.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Sale</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h2>Registrar New Sale</h2>
    <form action="../controllers/registerSaleController.php" method="post">
        <!-- Proceder a crear el formulario con 5 dropdowns fijos para cada venta, es decir,
        se pueden vender máximo 5 productos en cada venta (por ahora) -->
        <!-- Producto 1 -->
         <label>Producto 1:</label>
         <select name="product_id_1" id="product_id_1">
            <option value="">-- Seleccione Producto --</option>
            <?php foreach($all_products as $product): ?>
                <option value="<?php echo $product['product_id']; ?>">
                    <?php echo $product['product_name']; ?> (Stock: <?php echo $product['current_stock']; ?>)
                </option>
            <?php endforeach; ?>
         </select>
         <label>Cantidad:</label>
         <input type="number" name="quantity_1" id="quantity_1" min="0" value="0">
         <br><br>
        
        <!-- Producto 2 -->
         <label>Producto 2:</label>
         <select name="product_id_2" id="product_id_2">
            <option value="">-- Seleccione Producto --</option>
            <?php foreach($all_products as $product): ?>
                <option value="<?php echo $product['product_id']; ?>">
                    <?php echo $product['product_name']; ?> (Stock: <?php echo $product['current_stock']; ?>)
                </option>
            <?php endforeach; ?>
         </select>
         <label>Cantidad:</label>
         <input type="number" name="quantity_2" id="quantity_2" min="0" value="0">
         <br><br>

        <!-- Producto 3 -->
         <label>Producto 3:</label>
         <select name="product_id_3" id="product_id_3">
            <option value="">-- Seleccione Producto --</option>
            <?php foreach($all_products as $product): ?>
                <option value="<?php echo $product['product_id']; ?>">
                    <?php echo $product['product_name']; ?> (Stock: <?php echo $product['current_stock']; ?>)
                </option>
            <?php endforeach; ?>
         </select>
         <label>Cantidad:</label>
         <input type="number" name="quantity_3" id="quantity_3" min="0" value="0">
         <br><br>

        <!-- Producto 4 -->
         <label>Producto 4:</label>
         <select name="product_id_4" id="product_id_4">
            <option value="">-- Seleccione Producto --</option>
            <?php foreach($all_products as $product): ?>
                <option value="<?php echo $product['product_id']; ?>">
                    <?php echo $product['product_name']; ?> (Stock: <?php echo $product['current_stock']; ?>)
                </option>
            <?php endforeach; ?>
         </select>
         <label>Cantidad:</label>
         <input type="number" name="quantity_4" id="quantity_4" min="0" value="0">
         <br><br>

        <!-- Producto 5 -->
         <label>Producto 5:</label>
         <select name="product_id_5" id="product_id_5">
            <option value="">-- Seleccione Producto --</option>
            <?php foreach($all_products as $product): ?>
                <option value="<?php echo $product['product_id']; ?>">
                    <?php echo $product['product_name']; ?> (Stock: <?php echo $product['current_stock']; ?>)
                </option>
            <?php endforeach; ?>
         </select>
         <label>Cantidad:</label>
         <input type="number" name="quantity_5" id="quantity_5" min="0" value="0">
         <br><br>

        <button type="submit">Register Sale</button>

    </form>

</body>
</html>