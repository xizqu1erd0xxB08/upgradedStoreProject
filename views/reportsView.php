<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Esta vista normalmente se carga desde reportsController.php.
// El controlador crea estas variables; estos respaldos evitan "undefined variable"
// en VS Code y permiten mostrar reportes vacíos si alguien abre la vista directamente.
$totalRevenue = $totalRevenue ?? null;
$bestSelling = $bestSelling ?? null;
$worstSelling = $worstSelling ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reportes de Ventas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h2>📊 Reportes de Ventas</h2>
    
    <!-- Sección 1: Total Revenue -->
    <h3>💰 Total Vendido</h3>
    <?php if ($totalRevenue !== null): ?>
        <p>Total: $<?php echo $totalRevenue; ?></p>
    <?php else: ?>
        <p>Total: $0</p>
    <?php endif; ?>
    
    <hr>
    
    <!-- Sección 2: Best Selling -->
    <h3>🔥 Top 5 Productos Más Vendidos</h3>
    <?php if ($bestSelling !== null): ?>
    <table>
        <tr>
            <th>🔥 Nombre 🔥</th>
            <th>🔥 Cantidad vendida 🔥</th>
        </tr>
    
    <?php foreach($bestSelling as $product): ?>
        <tr>
            <td><?php echo $product['product_name']; ?></td>
            <td><?php echo $product['total_quantity']; ?></td>
        </tr>        
    <?php endforeach; ?>
        
    </table>
    <?php elseif (empty($bestSelling)): ?>
        <p>No hay productos más vendidos registrados</p>
    <?php endif; ?>

    <hr>

    <!-- Sección 3: Worst Selling -->
    <h3>📉 Top 5 Productos Menos Vendidos</h3>
    <?php if ($worstSelling !== null): ?>
        <table>
            <tr>
                <th>📉 Nombre 📉</th>
                <th>📉 Cantidad vendida 📉</th>
            </tr>
    <?php foreach($worstSelling as $product): ?>
        <tr>
            <td><?php echo $product['product_name']; ?></td>
            <td><?php echo $product['total_quantity']; ?></td>
        </tr>
    <?php endforeach; ?>

        </table>
    <?php elseif (empty($worstSelling)): ?>
        <p>No hay productos menos vendidos registrados</p>
    <?php endif; ?>
    
    <br>
    <a href="../views/dashboard.php">Volver al inicio</a>
</body>
</html>
