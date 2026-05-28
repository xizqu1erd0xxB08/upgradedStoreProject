<?php 
/* Crear la clase model.php para que sea la clase padre de todos los modelos para 
que obtengan la conexión, es decir, para que la hereden */

class Model {
    // 'protected' para que los demás modelos puedan acceder a esta conexión
    /** @var PDO  */
    protected $connection;

    // Todas las clases hijas heredan este constructor automáticamente
    public function __construct($connection)
    {
        $this->connection = $connection;
    }
}
?>