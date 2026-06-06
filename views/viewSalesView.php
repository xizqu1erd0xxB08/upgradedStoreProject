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

// Esta vista normalmente se carga desde viewSalesController.php.
// El controlador crea $arraySales; este respaldo evita "undefined variable"
// en VS Code y evita errores si alguien abre la vista directamente.
$arraySales = $arraySales ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h2>Historial de ventas</h2>

    <table>
        <tr>
            <th>ID Venta</th>
            <th>Fecha</th>
            <th>Total</th>
            <th>No. Productos</th>
            <th>Acciones</th>
        </tr>

        <?php foreach($arraySales as $sale): ?>
            <tr>
                <td><?php echo $sale['sale_id']; ?></td>
                <td><?php echo $sale['sale_date']; ?></td>
                <td><?php echo $sale['sale_total']; ?></td>
                <td><?php echo $sale['products_count']; ?></td>
                <td>
                    <form action="../controllers/viewSaleDetailsController.php" method="post" style="display: inline;">
                        <input type="hidden" name="saleId" value="<?php echo $sale['sale_id']; ?>">
                        <button type="submit">Ver Detalles</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
