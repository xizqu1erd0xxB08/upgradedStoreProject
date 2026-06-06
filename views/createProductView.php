<?php
session_start();

// Evitar caché para el botón de atrás
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Validar sesión
if (!isset($_SESSION['userId']) || !isset($_SESSION['userName'])) {
    header("Location: loginView.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear producto nuevo</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h2>Crea un producto nuevo</h2>
    <form action="../controllers/createProductController.php" method="post">

        <label for="productName">Nombre del producto:</label>
        <input type="text" id="productName" name="productName" required>
        <br><br>

        <label for="productPrice">Precio del producto:</label>
        <input type="number" id="productPrice" name="productPrice" required>
        <br><br>

        <label for="currentStock">Stock del producto</label>
        <input type="number" id="currentStock" name="currentStock" required>
        <br><br>

        <button type="submit">Crear producto</button>

    </form>
</body>
</html>