<?php 
require_once 'model.php'; // Incluir la clase padre que otorga el constructor de la conexión

// Product EXTIENDE Model: hereda $connection y el constructor
class Product extends Model {
    // Ya no se necesita declarar $connection ni el constructor porque están heredados de Model

    // Se pueden escribir los métodos directamente

    // Método createProduct
    public function createProduct($productName, $productPrice, $currentStock, $userId) {
        // Sanitizar $product_name 
        $product_name = trim($productName); // Quitar posibles espacios en blanco al inicio y al final

        // Verificar que $productName NO esté vacío 
        if (empty($productName)) {
                return ['success' => false, 'errorMessage' => 'El nombre del producto NO puede estar vacío'];
            }

        // Sanitizar $product_price
        $productPrice = (int)$productPrice; // Convertir $productPrice a tipo Int 

        // Verificar que el precio sea mayor a 0
        if ($productPrice <= 0) {
                return ['success' => false, 'errorMessage' => 'Debe ingresar un precio mayor a 0'];
            }
        
        // Sanitizar current_stock
        $currentStock = (int)$currentStock; // Convertir $productStock a tipo Int

        // Verificar que el stock no tenga un número negativo
        if ($currentStock < 0) {
                return ['success' => false, 'errorMessage' => 'El número del stock NO puede ser negativo'];
            }
        
        // Hacer el INSERT de producto con PDO

        $createProductStmt = $this->connection->prepare(
            "INSERT INTO products (product_name, product_price, current_stock, user_id) 
            VALUES (?, ?, ?, ?)"
        );
        
        // Hacer el execute() que recibe directamente el array de valores, bindea y ejecuta en una sola línea
        $createProductStmt->execute([$productName, $productPrice, $currentStock, $userId]);

        // Verificar que se insertó una fila en la base de datos
        if ($createProductStmt->rowCount() <= 0) {
            return ['success' => false, 'errorMessage' => 'Producto no creado en la base de datos'];
        }
        
        // Sin errores, retornar response array de éxito
        return ['success' => true, 'productId' => $this->connection->lastInsertId()];
    }

    // Método getProductsByUser() 
    public function getProductsByUser($user_id){
        // Crear el SELECT de productos por user_id
        $get_products_query = "SELECT product_id, product_name, product_price, current_stock, created_at, updated_at FROM products WHERE user_id = ? AND is_active = 1";

        // Preparar Statement
        $get_products_stmt = mysqli_prepare($this->connection, $get_products_query);

        // Bindear Statement
        mysqli_stmt_bind_param($get_products_stmt, 'i', $user_id);

        // Ejecutar Statement
        $get_products_stmt_executed = mysqli_stmt_execute($get_products_stmt);

        // Validar ejecución
        if (!$get_products_stmt_executed) {
            $get_products_stmt_error = mysqli_stmt_error($get_products_stmt);
            mysqli_stmt_close($get_products_stmt); 
            return ['success' => false, 'errorMessage' => 'Error en la ejecución del Statement: ' . $get_products_stmt_error];
        }

        // Obtener resultados
        $get_products_result = mysqli_stmt_get_result($get_products_stmt);

        // Validar resultados
        if (!$get_products_result) {
            $get_products_stmt_error = mysqli_stmt_error($get_products_stmt); 
            mysqli_stmt_close($get_products_stmt);
            return ['success' => false, 'errorMessage' => 'Error al obtener resultados: ' . $get_products_stmt_error];
        }

        // Obtener resultados usando un loop while
        $products = []; // Creamos un array vacío para almacenar los productos que vamos a obtener del resultado del statement
        while ($product = mysqli_fetch_assoc($get_products_result)) {
            $products[] = $product; // Agregamos cada producto al array de productos
        }

        // Cerrar Statement
        mysqli_stmt_close($get_products_stmt);

        // Retornar response array de éxito con productos
        return ['success' => true, 'products' => $products];

        /*
        En este método sí es necesario retornar los productos porque sí los vamos a usar en el frontend para mostrarlos, 
        a diferencia del método createProduct() que no necesitamos retornar el product_id porque no lo vamos a usar para 
        nada más, pero en este método sí necesitamos retornar los productos porque sí los vamos a usar para mostrarlos en 
        el frontend, entonces sí es necesario hacer un SELECT para obtenerlos y retornarlos en el response array.
        */ 
   }

   // Método deleteProduct
   public function deleteProduct($product_id, $user_id){
    $delete_product_query = "UPDATE products SET is_active = 0 WHERE product_id = ? AND user_id = ?"; // query DELETE

    $delete_product_stmt = mysqli_prepare($this->connection, $delete_product_query); // Preparar Statement

    mysqli_stmt_bind_param($delete_product_stmt, 'ii', $product_id, $user_id); // Bindear el Statement

    $delete_product_stmt_executed = mysqli_stmt_execute($delete_product_stmt); // Ejecutar el Statement

    // Verificar ejecución correcta del statement
    if (!$delete_product_stmt_executed || mysqli_stmt_affected_rows($delete_product_stmt) < 0) {
        $delete_product_stmt_error = mysqli_stmt_error($delete_product_stmt);
        mysqli_stmt_close($delete_product_stmt);
        return ['success' => false, 'errorMessage' => 'Error en la ejecución del Statement: ' . $delete_product_stmt_error];
    }

    // Verificar si la query se ejecutó, pero la database no se modificó
    if (mysqli_stmt_affected_rows($delete_product_stmt) === 0) {
        mysqli_stmt_close($delete_product_stmt);
        return ['success' => false, 'errorMessage' => 'Consulta DELETE ejecutada, pero base de datos NO modificada'];
    }

    // Todo ejecutado correctamente
    mysqli_stmt_close($delete_product_stmt);
    return ['success' => true];
   }

   // Método getProductById
   public function getProductById($product_id, $user_id){
    // SELECT con WHERE
    $get_product_by_id_query = "SELECT product_id, product_name, product_price, current_stock FROM products WHERE product_id = ? AND user_id = ? AND is_active = 1";
    
    $get_product_by_id_stmt = mysqli_prepare($this->connection, $get_product_by_id_query); // Preparar Statement

    mysqli_stmt_bind_param($get_product_by_id_stmt, 'ii', $product_id, $user_id); // Bindear Statement

    $get_product_by_id_stmt_executed = mysqli_stmt_execute($get_product_by_id_stmt); // Ejecutar Statement

    // Verificar ejecución del Statement
    if (!$get_product_by_id_stmt_executed) {
        $get_product_by_id_stmt_error = mysqli_stmt_error($get_product_by_id_stmt);
        mysqli_stmt_close($get_product_by_id_stmt);
        return ['success' => false, 'errorMessage' => 'Error en la ejecución del statement: ' . $get_product_by_id_stmt_error];
    }

    // Obtener resultados del Statement
    $get_product_by_id_result = mysqli_stmt_get_result($get_product_by_id_stmt);

    // Extraer resultado del Statement (como solo estamos extrayendo un solo producto, no usamos un loop)
    $product_by_id = mysqli_fetch_assoc($get_product_by_id_result);

    // Verificar si $product_by_id existe
    if ($product_by_id === null) {
        mysqli_stmt_close($get_product_by_id_stmt);
        return ['success' => false, 'errorMessage' => 'Producto no encontrado en la base de datos'];
    }

    // Éxito, retornar product
    mysqli_stmt_close($get_product_by_id_stmt);
    return [
        'success' => true,
        'product_by_id' => $product_by_id
    ];
   }

   // Método updateProduct()
   public function updateProduct($product_name, $product_price, $current_stock, $user_id, $product_id){
   // Copiamos la lógica de sanitización que usamos en el método createProduct()
    // Sanitizar $product_name
        $product_name = htmlspecialchars($product_name); // Nos aseguramos de quitar caracteres raros de html
        $product_name = trim($product_name); // Quitamos posibles espacios en blanco al inicio y al final 
        if(empty($product_name))
            {
                return ['success' => false, 'errorMessage' => 'El nombre del producto NO puede estar vacío'];
            }

        // Sanitizar $product_price
        $product_price = (int)$product_price; 
        if($product_price <= 0)
            {
                return ['success' => false, 'errorMessage' => 'Debe ingresar un precio mayor a 0'];
            }
        
        // Sanitizar current_stock
        $current_stock = (int)$current_stock;
        if($current_stock < 0)
            {
                return ['success' => false, 'errorMessage' => 'Debe tener al menos una unidad en stock'];
            } 
   
    // Query UPDATE
    $update_product_query = "UPDATE products SET product_name = ?, product_price = ?, current_stock = ? WHERE user_id = ? AND product_id = ?";
    
    $update_product_stmt = mysqli_prepare($this->connection, $update_product_query); // Preparar Statement

    mysqli_stmt_bind_param($update_product_stmt, 'siiii', $product_name, $product_price, $current_stock, $user_id, $product_id); // Bindear Statement

    $update_product_stmt_executed = mysqli_stmt_execute($update_product_stmt); // Ejecutar el Statement

    // Verificar ejecución del Statement
    if (!$update_product_stmt_executed || mysqli_stmt_affected_rows($update_product_stmt) < 0) {
        $update_product_stmt_error = mysqli_stmt_error($update_product_stmt);
        mysqli_stmt_close($update_product_stmt);
        return ['success' => false, 'errorMessage' => 'Ejecución del Statement fallida: ' . $update_product_stmt_error];
    }

    // Verificar actualización de los datos en la database
    if (mysqli_stmt_affected_rows($update_product_stmt) === 0) {
        mysqli_stmt_close($update_product_stmt);
        return ['success' => false, 'errorMessage' => 'Consulta SQL con Statement ejecutada, pero base de datos NO modificada'];
    }

    // Éxito
    mysqli_stmt_close($update_product_stmt);
    return ['success' => true];

   }
}
?>