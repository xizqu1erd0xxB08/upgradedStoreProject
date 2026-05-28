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
    <title>Create New Product</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h2>Create New Product</h2>
    <form action="../controllers/createProductController.php" method="post">

        <label for="product_name">Product name:</label>
        <input type="text" id="product_name" name="product_name" required>
        <br><br>

        <label for="product_price">Product price:</label>
        <input type="number" id="product_price" name="product_price" required>
        <br><br>

        <label for="current_stock">Current Stock:</label>
        <input type="number" id="current_stock" name="current_stock" required>
        <br><br>

        <button type="submit">Create product</button>

    </form>
</body>
</html>