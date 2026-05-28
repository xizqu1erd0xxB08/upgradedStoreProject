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
    <?php if ($total_revenue !== null): ?>
        <p>Total: $<?php echo $total_revenue['total_revenue']; ?></p>
    <?php else: ?>
        <p>Error: <?php echo $total_revenue_error; ?></p>
    <?php endif; ?>
    
    <hr>
    
    <!-- Sección 2: Best Selling (tu turno) -->
    <h3>🔥 Top 5 Productos Más Vendidos</h3>
    <?php if ($best_selling !== null): ?>
    <table>
        <tr>
            <th>🔥 Nombre 🔥</th>
            <th>🔥 Cantidad vendida 🔥</th>
        </tr>
    
    <?php foreach($best_selling as $product): ?>
        <tr>
            <td><?php echo $product['product_name']; ?></td>
            <td><?php echo $product['total_quantity']; ?></td>
        </tr>        
    <?php endforeach; ?>
        
    </table>
    <?php else: ?>
        <p>Error: <?php echo $best_selling_error; ?></p>
    <?php endif; ?>

    <hr>

    <!-- Sección 3: Worst Selling (tu turno) -->
    <h3>📉 Top 5 Productos Menos Vendidos</h3>
    <?php if ($worst_selling !== null): ?>
        <table>
            <tr>
                <th>📉 Nombre 📉</th>
                <th>📉 Cantidad vendida 📉</th>
            </tr>
    <?php foreach($worst_selling as $product): ?>
        <tr>
            <td><?php echo $product['product_name']; ?></td>
            <td><?php echo $product['total_quantity']; ?></td>
        </tr>
    <?php endforeach; ?>

        </table>
    <?php else: ?>
        <p>Error: <?php echo $worst_selling_error; ?></p>
    <?php endif; ?>
    
    <br>
    <a href="../views/dashboard.php">Volver al Dashboard</a>
</body>
</html>