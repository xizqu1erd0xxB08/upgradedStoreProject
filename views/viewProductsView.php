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
    <title>View Products</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h2>My Products</h2>
    <table class="table table-striped table-hover table-bordered">
        <tr>
            <th>Product Name:</th>
            <th>Product Price:</th>
            <th>Current Stock:</th>
            <th>Created At:</th>
            <th>Updated At:</th>
            <th>Actions:</th>
        </tr>

            <?php foreach ($array_products as $product): ?>
                <tr>
                    <td> <?php echo $product['product_name']; ?> </td>
                    <td> <?php echo $product['product_price']; ?> </td>
                    <td> <?php echo $product['current_stock']; ?> </td>
                    <td> <?php echo $product['created_at']; ?> </td>
                    <td> <?php echo $product['updated_at']; ?> </td>
                    <td>

                        <form action="formUpdateProductController.php" method="post" style="display: inline;">
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                            <button type="submit">Edit</button>
                        </form>
                        | 
                        <!-- Formulario MINI para DELETE con POST (más seguro) -->
                        <form action="../controllers/deleteProductController.php" method="post" style="display: inline;"> <!-- style="display: inline;" → El formulario NO ocupa espacio extra (queda en la misma línea) -->
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>"> <!-- <input type="hidden"> → Envía el product_id pero NO se ve en pantalla -->
                            <button type="submit">Delete</button> <!-- <button type="submit"> → Botón clickeable que envía el formulario por POST -->
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
    </table>
</body>
</html>