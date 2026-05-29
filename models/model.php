<?php 
/* Crear la clase model.php para que sea la clase padre de todos los modelos para 
que obtengan la conexión, es decir, para que la hereden */

class Model {
    /* 'protected' para que los demás modelos puedan acceder a esta conexión. 
    Al poner 'protected PDO $connection' ya está perfectamente documentado y tipado */
    protected PDO $connection;
 
    public function __construct(PDO $connection) // <- Type hint de la firma (PDO $connection)
    {
        $this->connection = $connection;
    }
}
?>