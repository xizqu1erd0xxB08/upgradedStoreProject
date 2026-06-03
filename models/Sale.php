<?php 
require_once 'Model.php';

class Sale extends Model {
    // Método registerSale()
    public function registerSale(string|int $userId, array $productsArray): array{
        try {
            // Empezar transacción
            $this->connection->beginTransaction();

            // Variables para acumular datos
            $saleTotal = 0;
            $validatedProducts = [];

            // Preparar UNA vez las consultas a la base de datos antes del bucle
            // 1. Query SELECT en la tabla 'products' para obtener los datos del producto
            $getProductInfo = $this->connection->prepare(
                "SELECT product_name, product_price, current_stock 
                 FROM products 
                 WHERE user_id = ? AND product_id = ?"
            );

            // 2. INSERT en la tabla 'sales'
            $insertSale = $this->connection->prepare(
                "INSERT INTO sales (user_id, sale_total) VALUES (?, ?)"
            );

            // 3. INSERT en la tabla 'sale_detail'
            $insertSaleDetail = $this->connection->prepare(
                "INSERT INTO sale_detail (sale_id, product_id, quantity, unit_price, subtotal) 
                 VALUES (?, ?, ?, ?, ?)"                
            );

            // 4. UPDATE en la tabla 'products' (restar stock)
            $updateStock = $this->connection->prepare(
                "UPDATE products SET current_stock = current_stock - ? 
                 WHERE product_id = ?"
            );

            // LOOP 1: Validar cada producto y preparar datos
            foreach ($productsArray as $product) {
                $productId = $product['productId'];
                $quantity = $product['quantity'];

                // Bindear + Ejecutar statement
                $getProductInfo->execute([$userId, $productId]);

                // Extraer datos del producto en un array asociativo
                $productInfo = $getProductInfo->fetch();
                
                /* Validar existencia del producto, si no se encuentra ningún registro que coincida con la consulta, 
                fetch() retornará 'false', Al usar ! (not), PHP evalúa si $productInfo es false */
                if (!$productInfo) {
                    throw new Exception('Producto no encontrado o no te pertenece');  
                }

                // Guardar la información obtenida del producto en variables para mantener más claridad en el código
                $productName = $productInfo['product_name'];
                $productPrice = $productInfo['product_price'];
                $currentStock = $productInfo['current_stock'];

                /* Validar que haya stock suficiente para el producto seleccionado, si no hay suficiente, se retornará 
                un mensaje de error */
                if ($quantity > $currentStock) {
                    throw new Exception("Stock insuficiente para $productName. Disponible: $currentStock, solicitado: 
                    $quantity");
                }

                // Calcular subtotal
                $subtotal = $quantity * $productPrice;
                
                // Añadir subtotal al total
                $saleTotal += $subtotal;

                // Guardar producto validado para usarlo en el loop 2
                $validatedProducts[] = [
                    'productId' => $productId,
                    'quantity' => $quantity,
                    'productPrice' => $productPrice,
                    'subtotal' => $subtotal
                ];
            }

            /* Ya que se validaron todos los productos y se tiene $saleTotal, hay que hacer los bindeos correspondientes
            en las tablas involucradas en esta operación (sales y sale_detail) */
        
            // Ejecutar + Bindear parámetros
            $insertSale->execute([$userId, $saleTotal]);

            // Obtener el ID de la venta recién creado
            $saleId = $this->connection->lastInsertId();

            // LOOP 2: Insertar detalles y actualizar inventario
            foreach ($validatedProducts as $validatedProduct) {            
                // Ejecutar + Bindear parámetros (insertar detalle de venta)
                $insertSaleDetail->execute([$saleId, $validatedProduct['productId'], $validatedProduct['quantity'],
                $validatedProduct['productPrice'], $validatedProduct['subtotal']]);
                
                // Ejecutar + Bindear parámetros (actualizar stock de productos)
                $updateStock->execute([$validatedProduct['quantity'], $validatedProduct['productId']]);

            }

            // Agregar commit de éxito en la transacción
            $this->connection->commit();

            // Retornar response array de éxito
            return ['success' => true, 'saleId' => $saleId];

        } catch (Exception $error) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack(); // Rollback para indicar que algo falló
            }
        
            // Retornar response array de error
            return ['success' => false, 'errorMessage' => $error->getMessage()];
        }
    }

    // Método getSalesByUser()
    public function getSalesByUser(string|int $userId): array {
        try {
            /* Query con JOIN entre la tabla 'sales' y 'sale_detail' porque se necesita que la ID en la table ventas
            coincida con la ID de la tabla sale_detail para poder, en dado caso que el usuario lo desee, mostrar el de-
            talle de la venta mostrada, ese es el propósito de este método, obtener todas las ventas por usuario, y te-
            ner la posibilidad de mostrar el detalle de una venta en caso de que el usuario lo desee. Por otro lado, 
            el COUNT sirve para contar cuántos productos (filas en sale_detail) tiene cada venta. el 'ORDER BY 
            sales.sale_date DESC' quien ordena cronológicamente de más reciente a más antigua. */
            $getSalesByUser = $this->connection->prepare(
                "SELECT 
                sales.sale_id,
                sales.sale_date,
                sales.sale_total,
                COUNT(sale_detail.sale_detail_id) AS products_count
                FROM sales
                INNER JOIN sale_detail ON sales.sale_id = sale_detail.sale_id
                WHERE sales.user_id = ?
                GROUP BY sales.sale_id
                ORDER BY sales.sale_date DESC"
            );

            $getSalesByUser->execute([$userId]);
        
            // Guardar el resultado de las ventas en un array asociativo
            $salesArray = $getSalesByUser->fetchAll();

            // Verificar que el array de ventas retornado no esté vacío
            if (empty($salesArray)) {
                throw new Exception("No se han encontrado ventas registradas");
            }

            // Retornar response array con éxito y array asociativo de ventas
            return ['success' => true, 'sales' => $salesArray];

        } catch (Exception $e) {
            return ['success'=> false, 'errorMessage' => $e->getMessage()];
        }

    }

    // Método getSaleDetails()
    public function getSaleDetails(string|int $saleId): array {
        try {
            $getSaleDetails = $this->connection->prepare(
                "SELECT 
                 products.product_name,
                 sale_detail.quantity,
                 sale_detail.unit_price,
                 sale_detail.subtotal
                 FROM sale_detail
                 INNER JOIN products ON sale_detail.product_id = products.product_id
                 WHERE sale_detail.sale_id = ?"
            );

            $getSaleDetails->execute([$saleId]);

            // Guardar el detalle de la venta en un array asociativo
            $saleDetails = $getSaleDetails->fetchAll();

            // Verificar que el detalle de la venta se haya encontrado en la base de datos
            if (empty($saleDetails)) {
                throw new Exception("Detalle de la venta no encontrado");
            }
            
            // Retornar detalle de la venta
            return['success' => true, 'saleDetails' => $saleDetails];

        } catch (Exception $e) {
            return ['success' => false, 'errorMessage' => $e->getMessage()];
        }
       
    }

    // Método getTotalRevenue (Total de dinero obtenido por el usuario en toda su historia)
    public function getTotalRevenue(string|int $userId): array {
        try {
            /* Filtrar las ventas por el ID del usuario y suma los valores de la columna 'sale_total' para retornar el 
            total de ingresos como 'total_revenue'. Cuando se usa SUM() en MySQL y no hay ninguna fila que sume, MySQL 
            no retorna cero filas. Retorna exactamente una fila con el valor NULL en la columna sumada.*/
            $getTotalRevenue = $this->connection->prepare(
                "SELECT SUM(sale_total) AS total_revenue
                 FROM sales 
                 WHERE user_id = ?"
            );

            $getTotalRevenue->execute([$userId]);

            $totalRevenue = $getTotalRevenue->fetch(); 

            // Verificar que el valor de la suma no sea NULL (ocurre cuando no hay ventas)
            if ($totalRevenue['total_revenue'] === null) { 
                return ['success' => true, 'totalRevenue' => 0];
            }

            return ['success' => true, 'totalRevenue' => (int)$totalRevenue['total_revenue']];
   
        } catch (Exception $e) {
            return ['success' => false, 'errorMessage' => $e->getMessage()];
        }

    }

    // Método bestSellingProducts (productos más vendidos)
    public function bestSellingProducts(string|int $userId): array {
        try {
            $getBestSellingProducts = $this->connection->prepare(
                "SELECT products.product_name, SUM(sale_detail.quantity) AS total_quantity 
                 FROM sale_detail 
                 INNER JOIN products ON products.product_id = sale_detail.product_id
                 INNER JOIN sales ON sales.sale_id = sale_detail.sale_id 
                 WHERE sales.user_id = ? 
                 GROUP BY sale_detail.product_id 
                 ORDER BY total_quantity 
                 DESC LIMIT 5"
        );

        $getBestSellingProducts->execute([$userId]);

        $bestSellingProducts = $getBestSellingProducts->fetchAll();
        
        if (empty($bestSellingProducts)) {
            throw new Exception("No hay productos más vendidos registrados");
        }

        return ['success' => true, 'bestSellingProducts' => $bestSellingProducts];
  
        } catch (Exception $e) {
            return ['success' => false, 'errorMessage' => $e->getMessage()];
        }

    }

    // Método worstSellingProducts()
    public function worstSellingProducts(string|int $userId): array {
        try {
            $getWorstSellingProducts = $this->connection->prepare(
                "SELECT products.product_name, SUM(sale_detail.quantity) AS total_quantity
                 FROM sale_detail
                 INNER JOIN products ON products.product_id = sale_detail.product_id
                 INNER JOIN sales ON sales.sale_id = sale_detail.sale_id
                 WHERE sales.user_id = ?
                 GROUP BY sale_detail.product_id
                 ORDER BY total_quantity
                 ASC LIMIT 5"
            );

            $getWorstSellingProducts->execute([$userId]);

            $worstSellingProducts = $getWorstSellingProducts->fetchAll();

            if (empty($worstSellingProducts)) {
                throw new Exception("No hay productos menos vendidos registrados");
            }

            return ['success' => true, 'worstSellingProducts' => $worstSellingProducts];

        } catch (Exception $e) {
            return ['success' => false, 'errorMessage' => $e->getMessage()];
        }

    }
}
?>