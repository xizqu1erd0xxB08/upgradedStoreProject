<?php 
require_once 'Database.php';

class Sale {
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    // Método registerSale()
    public function registerSale($user_id, $products_array){
        mysqli_begin_transaction($this->connection); // Empezar transacción

        try {
            // Variables para acumular datos
            $sale_total = 0;
            $validated_products = [];

            // LOOP 1: Validar cada producto y preparar datos
            foreach($products_array as $product){
                $product_id = $product['product_id'];
                $quantity = $product['quantity'];

                // Query SELECT para obtener los datos del producto
                $get_product_info_query = "SELECT product_name, product_price, current_stock FROM products WHERE user_id = ? AND product_id = ? ";

                $get_product_info_stmt = mysqli_prepare($this->connection, $get_product_info_query); // Preparar Statement

                mysqli_stmt_bind_param($get_product_info_stmt, 'ii', $user_id, $product_id); // Bindear Statement

                $get_product_info_stmt_executed = mysqli_stmt_execute($get_product_info_stmt); // Ejecutar Statement

                // Validar ejecución correcta del Statement
                if (!$get_product_info_stmt_executed) {
                    $get_product_info_stmt_error = mysqli_stmt_error($get_product_info_stmt);
                    mysqli_stmt_close($get_product_info_stmt);
                    return ['success' => false, 'errorMessage' => 'Error en la ejecución del Statement: ' . $get_product_info_stmt_error];
                }

                $get_product_info_stmt_result = mysqli_stmt_get_result($get_product_info_stmt); // Obtener datos del Statement

                // Extraer datos
                $product_info = mysqli_fetch_assoc($get_product_info_stmt_result);
                
                // Validar existencia del producto
                if ($product_info === null) {
                    throw new Exception('Producto no encontrado o no te pertenece');  
                }

                // Extraer datos a variables (para claridad)
                $product_name = $product_info['product_name'];
                $product_price = $product_info['product_price'];
                $current_stock = $product_info['current_stock'];

                // Cerrar Statement 
                mysqli_stmt_close($get_product_info_stmt);

                // Validar que haya stock suficiente
                if ($quantity > $current_stock) {
                    throw new Exception("Stock insuficiente para $product_name. Disponible: $current_stock, solicitado: $quantity");
                }

                // Calcular subtotal
                $subtotal = $quantity * $product_price;
                
                // Añadir subtotal al total
                $sale_total += $subtotal;

                // Guardar producto validado para usarlo en el loop 2
                $validated_products[] = [
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'unit_price' => $product_price,
                    'subtotal' => $subtotal
                ];
            }

            // Ya validamos todos los productos y tenemos $sale_total, ahora, hacemos los INSERT correspondientes
            // INSERT en la tabla sales
            $insert_sale_query = "INSERT INTO sales (user_id, sale_total) VALUES (?, ?)";

            $insert_sale_stmt = mysqli_prepare($this->connection, $insert_sale_query); // Preparar Statement

            mysqli_stmt_bind_param($insert_sale_stmt, 'ii', $user_id, $sale_total); // Bindear Statement

            $insert_sale_stmt_executed = mysqli_stmt_execute($insert_sale_stmt); // Ejecutar Statement

            // Validar que el Statement se haya ejecutado correctamente
            if (!$insert_sale_stmt_executed) {
                $insert_sale_stmt_error = mysqli_stmt_error($insert_sale_stmt);
                mysqli_stmt_close($insert_sale_stmt);
                throw new Exception("Error al crear venta: " . $insert_sale_stmt_error);
            }

            // Obtener el sale_id recién creado
            $sale_id = mysqli_insert_id($this->connection);

            mysqli_stmt_close($insert_sale_stmt); // Cerrar Statement

            // LOOP 2: Insertar detalles y actualizar inventario
            foreach($validated_products as $validated_product){
                // INSERT en sale_detail
                $insert_sale_detail_query = "INSERT INTO sale_detail (sale_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)";

                $insert_sale_detail_stmt = mysqli_prepare($this->connection, $insert_sale_detail_query); // Preparar Statement

                // Bindear Statement
                mysqli_stmt_bind_param($insert_sale_detail_stmt, 'iiiii', 
                $sale_id, 
                $validated_product['product_id'], 
                $validated_product['quantity'], 
                $validated_product['unit_price'], 
                $validated_product['subtotal']);
                
                $insert_sale_detail_stmt_executed = mysqli_stmt_execute($insert_sale_detail_stmt); // Ejecutar Statement

                // Verificar ejecución del Statement
                if (!$insert_sale_detail_stmt_executed) {
                    $insert_sale_detail_stmt_error = mysqli_stmt_error($insert_sale_detail_stmt);
                    mysqli_stmt_close($insert_sale_detail_stmt); // Cerrar Statement
                    throw new Exception("Error al insertar detalle de la venta: " . $insert_sale_detail_stmt_error);
                }

                mysqli_stmt_close($insert_sale_detail_stmt); // Éxito, cerrar Statement

                // UPDATE products (restar stock)
                $update_stock_query = "UPDATE products SET current_stock = current_stock - ? WHERE product_id = ?";

                $update_stock_stmt = mysqli_prepare($this->connection, $update_stock_query); // Preparar Statement

                mysqli_stmt_bind_param($update_stock_stmt, 'ii', $validated_product['quantity'], $validated_product['product_id']); // Bindear Statement

                $update_stock_stmt_executed = mysqli_stmt_execute($update_stock_stmt);

                // Validar ejecución del Statement que actualiza inventario
                if (!$update_stock_stmt_executed) {
                    $update_stock_stmt_error = mysqli_stmt_error($update_stock_stmt);
                    mysqli_stmt_close($update_stock_stmt);
                    throw new Exception('Error al actualizar inventario: ' . $update_stock_stmt_error);
                }

                mysqli_stmt_close($update_stock_stmt);
            }

            // Éxito, hacer commit de la transacción
            mysqli_commit($this->connection);

            // Retornar response array de éxito
            return ['success' => true, 'sale_id' => $sale_id];
        } catch (Exception $error) {
            mysqli_rollback($this->connection); // Rollback para indicar que algo falló
            
            // Retornar response array de error
            return ['success' => false, 'errorMessage' => $error->getMessage()];
        }
    }

    // Método getSalesByUser()
    public function getSalesByUser($user_id){
        // Query con JOIN y COUNT
        $get_sales_by_user_query = "SELECT 
            sales.sale_id,
            sales.sale_date,
            sales.sale_total,
            COUNT(sale_detail.sale_detail_id) AS products_count
        FROM sales
        INNER JOIN sale_detail ON sales.sale_id = sale_detail.sale_id
        WHERE sales.user_id = ?
        GROUP BY sales.sale_id
        ORDER BY sales.sale_date DESC";

        $get_sales_by_user_stmt = mysqli_prepare($this->connection, $get_sales_by_user_query); // Statement

        mysqli_stmt_bind_param($get_sales_by_user_stmt, 'i', $user_id); // Bindeo de Statement

        $get_sales_by_user_stmt_executed = mysqli_stmt_execute($get_sales_by_user_stmt); // Ejecución de Statement

        // Validar ejecución del Statement
        if (!$get_sales_by_user_stmt_executed) {
            $get_sales_by_user_stmt_error = mysqli_stmt_error($get_sales_by_user_stmt);
            mysqli_stmt_close($get_sales_by_user_stmt);
            return ['success' => false, 'errorMessage' => 'Error al consultar ventas: ' . $get_sales_by_user_stmt_error];
        }

        // Obtener resultados para mostrar las ventas
        $get_sales_by_user_result = mysqli_stmt_get_result($get_sales_by_user_stmt);

        // Obtener los resultados de las ventas extraidos usando un loop while
        $sales = []; // Se crea un array vacío para almecenar las ventas que se irán guardando a lo largo del loop, esto con el fin de poder usar este array para mostrar sus datos
        while ($sale = mysqli_fetch_assoc($get_sales_by_user_result)) {
            $sales[] = $sale; // Agregar cada venta al array de ventas creado anteriormente
        }

        mysqli_stmt_close($get_sales_by_user_stmt); // Cerrar Statement

        // Retornar response array con éxito y array de ventas
        return ['success' => true, 'sales' => $sales];

    }

    // Método getSaleDetails()
    public function getSaleDetails($sale_id){
        $get_sale_detail_query = "SELECT 
            products.product_name,
            sale_detail.quantity,
            sale_detail.unit_price,
            sale_detail.subtotal
        FROM sale_detail
        INNER JOIN products ON sale_detail.product_id = products.product_id
        WHERE sale_detail.sale_id = ?";

        $get_sale_detail_stmt = mysqli_prepare($this->connection, $get_sale_detail_query);

        mysqli_stmt_bind_param($get_sale_detail_stmt, 'i', $sale_id);

        if (!mysqli_stmt_execute($get_sale_detail_stmt)) {
            $error = mysqli_stmt_error($get_sale_detail_stmt);
            mysqli_stmt_close($get_sale_detail_stmt);
            return ['success' => false, 'errorMessage' => 'Error en ver detallas de la tarea.' . $error];
        }

        $get_sale_detail_result = mysqli_stmt_get_result($get_sale_detail_stmt);

        $sale_details = [];

        while ($detail = mysqli_fetch_assoc($get_sale_detail_result)) {
            $sale_details[] = $detail;
        }

        if (empty($sale_details)) {
            return['success' => false, 'errorMessage' => 'Venta no encontrada en la base de datos'];
        }

        mysqli_stmt_close($get_sale_detail_stmt);
        
        return[
            'success' => true,
            'sale_details' => $sale_details
        ];
    }

    // Método getTotalRevenue (total vendido)
    public function getTotalRevenue($user_id){
        $total_revenue_query = "SELECT SUM(sale_total) AS total_revenue
                                FROM sales 
                                WHERE user_id = ?";
        $total_revenue_stmt = mysqli_prepare($this->connection, $total_revenue_query);

        mysqli_stmt_bind_param($total_revenue_stmt, 'i', $user_id);

        if (!mysqli_stmt_execute($total_revenue_stmt)) {
            $e = mysqli_stmt_error($total_revenue_stmt);
            mysqli_stmt_close($total_revenue_stmt);
            return [
                'success' => false, 
                'errorMessage' => 'Error del Statement al mostrar total recaudado: ' . $e
                ];
        }

        $total_revenue_result = mysqli_stmt_get_result($total_revenue_stmt);
        
        $total_revenue = mysqli_fetch_assoc($total_revenue_result);

        if ($total_revenue === null) { 
            return [
                'success' => false,
                'errorMessage' => 'No hay ventas registradas hasta el momento, empieza a vender!'
                ];
        }

        if ($total_revenue['total_revenue'] === 0) {
            return [
                'success' => false,
                'errorMessage' => 'Todas tus ventas fueron gratis! Tienes 0 ingresos! Empieza a vender con buenos precios!'
            ];
        }

        mysqli_stmt_close($total_revenue_stmt);
        return [
            'success' => true,
            'total_revenue' => $total_revenue 
        ];

    }

    // Método bestSellingProducts
    public function bestSellingProducts($user_id){
        $best_selling_products_query = "SELECT products.product_name, SUM(sale_detail.quantity) AS total_quantity 
                                        FROM sale_detail 
                                        INNER JOIN products ON products.product_id = sale_detail.product_id
                                        INNER JOIN sales ON sales.sale_id = sale_detail.sale_id 
                                        WHERE sales.user_id = ? 
                                        GROUP BY sale_detail.product_id 
                                        ORDER BY total_quantity 
                                        DESC LIMIT 5";
        $best_selling_products_stmt = mysqli_prepare($this->connection, $best_selling_products_query);
        mysqli_stmt_bind_param($best_selling_products_stmt, 'i', $user_id);
        if (!mysqli_stmt_execute($best_selling_products_stmt)) {
            $error = mysqli_stmt_error($best_selling_products_stmt);
            mysqli_stmt_close($best_selling_products_stmt);
            return [
                'success' => false,
                'errorMessage' => 'Error del Statement al mostrar top 5 productos más vendidos. ' . $error
            ];
        }

        $best_selling_products_result = mysqli_stmt_get_result($best_selling_products_stmt);
        $best_selling_products = [];
        while ($best_selling_products_row = mysqli_fetch_assoc($best_selling_products_result)) {
            $best_selling_products[] = $best_selling_products_row;
        }

        if (empty($best_selling_products)) {
            mysqli_stmt_close($best_selling_products_stmt);
            return [
                'success' => false,
                'errorMessage' => 'No hay productos más vendidos registrados, empieza a vender al por mayor!' 
            ];
        }

        mysqli_stmt_close($best_selling_products_stmt);
        return [
            'success' => true,
            'best_selling_products' => $best_selling_products
        ];
    }

    // Método worstSellingProducts()
    public function worstSellingProducts($user_id){
        $worst_selling_products_query = "SELECT products.product_name, SUM(sale_detail.quantity) AS total_quantity
                                        FROM sale_detail
                                        INNER JOIN products ON products.product_id = sale_detail.product_id
                                        INNER JOIN sales ON sales.sale_id = sale_detail.sale_id
                                        WHERE sales.user_id = ?
                                        GROUP BY sale_detail.product_id
                                        ORDER BY total_quantity
                                        ASC LIMIT 5";
        $worst_selling_products_stmt = mysqli_prepare($this->connection, $worst_selling_products_query);
        mysqli_stmt_bind_param($worst_selling_products_stmt, 'i', $user_id);

        if (!mysqli_stmt_execute($worst_selling_products_stmt)) {
            $error = mysqli_stmt_error($worst_selling_products_stmt);
            mysqli_stmt_close($worst_selling_products_stmt);
            return [
                'success' => false,
                'errorMessage' => 'Error del Statement al mostrar top 5 productos menos vendidos. ' . $error
            ];
        }

        $worst_selling_products_result = mysqli_stmt_get_result($worst_selling_products_stmt);

        $worst_selling_products = [];

        while ($worst_selling_products_row = mysqli_fetch_assoc($worst_selling_products_result)) {
            $worst_selling_products[] = $worst_selling_products_row;
        }

        if (empty($worst_selling_products)) {
            mysqli_stmt_close($worst_selling_products_stmt);
            return [
                'success' => false,
                'errorMessage' => 'No hay productos menos vendidos, porque ni siquiera haz vendido crack xd'
            ];
        }

        mysqli_stmt_close($worst_selling_products_stmt);
        return [
            'success' => true,
            'worst_selling_products' => $worst_selling_products
        ];
    }
}
?>