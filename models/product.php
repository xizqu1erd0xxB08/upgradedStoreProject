<?php 
require_once 'model.php'; // Incluir la clase padre que otorga el constructor de la conexión

// Product EXTIENDE Model: hereda $connection y el constructor
class Product extends Model {
    // Ya no se necesita declarar $connection ni el constructor porque están heredados de Model

    // Se pueden escribir los métodos directamente

    // Refactorizar todos los métodos con PDO
    // Método createProduct
    /* Al poner ': array', se le asegura a PHP que el resultado final será estrictamente un arreglo. */
    public function createProduct(string $productName, string|int $productPrice, string|int $currentStock, string|int $userId): array {
        // Sanitizar $product_name 
        $productName = trim($productName); // Quitar posibles espacios en blanco al inicio y al final

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
        
        /* Hacer el INSERT de producto con PDO. Agregarlo dentro de un bloque try-catch porque PDO 
        lanza excepciones cuando ATTR_ERRMODE_EXCEPTION está activo */
        try {
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

        } catch (PDOException $e) {
            /* Capturar el error de PDO y retornarlo en el formato estándar que se ha venido manejando
            (response arrays) */
            return ['success' => false, 'errorMessage' => 'Error en la base de datos: ' . $e->getMessage()];
        }
        
    }

    // Método getProductsByUser() 
    public function getProductsByUser(string|int $userId): array {
        try {
            $getProductsByUserStmt = $this->connection->prepare(
                "SELECT product_id, product_name, product_price, current_stock, created_at, updated_at
                 FROM products
                 WHERE user_id = ? AND is_active = 1"
            );

            $getProductsByUserStmt->execute([$userId]);

            /* fetchAll() retorna un array con todas las filas gracias ala configuración hecha en 
            database.php, ATTR_DEFAULT_FETCH_MODE. Ya no se necesita el loop while de mysqli */
            $allProducts = $getProductsByUserStmt->fetchAll();

            return ['success' => true, 'products' => $allProducts];

        } catch (PDOException $e) {
            return ['success' => false, 'errorMessage' => 'Error en la base de datos:' . $e->getMessage()];
        }
    
   }

    // Método getProductById
    public function getProductById(string|int $productId, string|int $userId): array {
        try {
            $getProductByIdStmt = $this->connection->prepare(
                "SELECT product_id, product_name, product_price, current_stock 
                FROM products 
                WHERE product_id = ? AND user_id = ? AND is_active = 1"
            );

            $getProductByIdStmt->execute([$productId, $userId]);

            /* fetch() retorna solo una fila, no un array de filas. Si no encuentra nada, retorna
            false */
            $productById = $getProductByIdStmt->fetch();

            if ($productById === false) {
                return ['success' => false, 'errorMessage' => 'Producto no encontrado o no te pertenece'];
            }

            return ['success' => true, 'productById' => $productById];

        } catch (PDOException $e) {
            return ['success' => false, 'errorMessage' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    
   }

    // Método updateProduct()
    public function updateProduct(string $productName, string|int $productPrice, string|int $currentStock, string|int $userId, string|int $productId): array {
        $productName = trim($productName); // Quitar posibles espacios en blanco al inicio y al final

        // Verificar que $productName NO esté vacío 
        if (empty($productName)) {
            return ['success' => false, 'errorMessage' => 'El nombre del producto NO puede estar vacío'];
        }

        $productPrice = (int)$productPrice; // Convertir $productPrice a tipo Int 

        // Verificar que el precio sea mayor a 0
        if ($productPrice <= 0) {
            return ['success' => false, 'errorMessage' => 'Debe ingresar un precio mayor a 0'];
        }
        
        $currentStock = (int)$currentStock; // Convertir $productStock a tipo Int

        // Verificar que el stock no tenga un número negativo
        if ($currentStock < 0) {
            return ['success' => false, 'errorMessage' => 'El número del stock NO puede ser negativo'];
        }

        try {
            $updateProductStmt = $this->connection->prepare(
            "UPDATE products 
            SET product_name = ?, product_price = ?, current_stock = ? 
            WHERE user_id = ? AND product_id = ?"
            );

            $updateProductStmt->execute([$productName, $productPrice, $currentStock, $userId, $productId]); 

            if ($updateProductStmt->rowCount() < 0) {
                return ['success' => false, 'errorMessage' => 'Error al actualizar producto. Filas afectadas: 0'];
            }

            /* rowCount() = 0 puede significar que el producto no se encontró o que los datos
            eran idénticos. */
            if ($updateProductStmt->rowCount() === 0) {
                return ['success' => false, 'errorMessage' => 'Producto no encotrado o datos sin cambios'];
            }

            return ['success' => true];

        } catch (PDOException $e) {
            return ['success' => false, 'errorMessage' => 'Error en la base de datos: ' . $e->getMessage()];
        }

   }

   // Método deleteProduct
   public function deleteProduct(string|int $productId, string|int $userId): array {
    try {
        $deleteProductStmt = $this->connection->prepare(
            "UPDATE products SET is_active = 0 
             WHERE product_id = ? AND user_id = ?"
        );

        $deleteProductStmt->execute([$productId, $userId]);

        if ($deleteProductStmt->rowCount() <= 0) {
            return ['success' => false, 'errorMessage' => 'Error al eliminar producto'];
        }

        return ['success' => true];

    } catch (PDOException $e) {
        return ['success' => false, 'errorMessage' => 'Error en la base de datos: ' . $e->getMessage()];
    }
   }

}
?>