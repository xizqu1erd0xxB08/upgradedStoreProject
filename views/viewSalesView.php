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

        <?php foreach($array_sales as $sale): ?>
            <tr>
                <td><?php echo $sale['sale_id']; ?></td>
                <td><?php echo $sale['sale_date']; ?></td>
                <td><?php echo $sale['sale_total']; ?></td>
                <td><?php echo $sale['products_count']; ?></td>
                <td>
                    <form action="../controllers/viewSaleDetailsController.php" method="post" style="display: inline;">
                        <input type="hidden" name="sale_id" value="<?php echo $sale['sale_id']; ?>">
                        <button type="submit">Ver Detalles</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>