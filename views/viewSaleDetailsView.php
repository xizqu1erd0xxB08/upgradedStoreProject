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

// Esta vista normalmente se carga desde viewSaleDetailsController.php.
// El controlador crea $arraySaleDetails; este respaldo evita "undefined variable"
// en VS Code y evita errores si alguien abre la vista directamente.
$arraySaleDetails = $arraySaleDetails ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Venta</title>
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

            <?php foreach($arraySaleDetails as $saleDetails): ?>
                <tr>
                    <td><?php echo $saleDetails['product_name']; ?></td>
                    <td><?php echo $saleDetails['quantity']; ?></td>
                    <td><?php echo $saleDetails['unit_price']; ?></td>
                    <td><?php echo $saleDetails['subtotal']; ?></td>
                </tr>
            <?php endforeach; ?>

    </table>
    <br>
    <a href="../controllers/viewSalesController.php">Volver al historial</a>
</body>
</html>
