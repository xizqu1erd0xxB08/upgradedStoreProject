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

// Esta vista normalmente se carga desde viewProductsController.php.
// El controlador crea $allProducts; este respaldo evita "undefined variable"
// en VS Code y evita errores si alguien abre la vista directamente.
$allProducts = $allProducts ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver productos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h2 id="productos">Mis productos</h2>
    <table class="table table-striped table-hover table-bordered">
        <tr>
            <th>Nombre del producto:</th>
            <th>Precio del producto:</th>
            <th>Stock del producto:</th>
            <th>Creado el día:</th>
            <th>Actualizado el día:</th>
            <th>Acciones:</th>
        </tr>

            <?php foreach ($allProducts as $product): ?>
                <tr>
                    <td> <?php echo $product['product_name']; ?> </td>
                    <td> <?php echo $product['product_price']; ?> </td>
                    <td> <?php echo $product['current_stock']; ?> </td>
                    <td> <?php echo $product['created_at']; ?> </td>
                    <td> <?php echo $product['updated_at']; ?> </td>
                    <td>

                        <form action="../controllers/formUpdateProductController.php" method="post" style="display: inline;">
                            <input type="hidden" name="productId" value="<?php echo $product['product_id']; ?>">
                            <button type="submit">Editar</button>
                        </form>
                        | 
                        <!-- Formulario MINI para DELETE con POST (más seguro) -->
                        <form action="../controllers/deleteProductController.php" method="post" style="display: inline;"> <!-- style="display: inline;" → El formulario NO ocupa espacio extra (queda en la misma línea) -->
                            <input type="hidden" name="productId" value="<?php echo $product['product_id']; ?>"> <!-- <input type="hidden"> → Envía el product_id pero NO se ve en pantalla -->
                            <button type="submit">Eliminar</button> <!-- <button type="submit"> → Botón clickeable que envía el formulario por POST -->
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
    </table>
</body>
</html>
