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
    <title>Detalles de Venta</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h2>Detalles de la venta</h2>

    <table>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
        </tr>

            <?php foreach($array_sale_details as $sale_details): ?>
                <tr>
                    <td><?php echo $sale_details['product_name']; ?></td>
                    <td><?php echo $sale_details['quantity']; ?></td>
                    <td><?php echo $sale_details['unit_price']; ?></td>
                    <td><?php echo $sale_details['subtotal']; ?></td>
                </tr>
            <?php endforeach; ?>

    </table>
    <br>
    <a href="../controllers/viewSalesController.php">Volver al historial</a>
</body>
</html>