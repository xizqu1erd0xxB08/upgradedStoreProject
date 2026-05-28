<?php 
// Clase Database mejorado con PDO 
class Database {
    /* Propiedad privada para la conexión porque solo Database necesita manejar la conexión. 
    Las clases hijas (Product, User, Sale) recibirán la conexión ya lista. */
    private $connection;

    // Constructor que junta los parámetros para realizar la conexión a la base de datos:
    public function __construct($hostName, $hostAdmin, $hostAdminPassword, $databaseName) {
        // Construir el Data Source Name (DSN)
        $dsn = "mysql:host=$hostName;dbname=$databaseName;charset=utfmb4";

        /* charset=utfmb4 es importante porque soporta emojis y caracteres especiales, utf8 normal 
        solamente soporta hasta 3 bytes por caracter; utf8mb4 soporta 4 */

        // new PDO() puede lanzar una excepción si falla, por eso se envuelve en try-catch
        try {
            $this->connection = new PDO($dsn, $hostAdmin, $hostAdminPassword);

            /* Asignar atributo para que PDO lance excepciones en vez de retornar false, es la 
            forma correcta de manejar errores en PDO */
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            /* Asignar atributo para que PDO retorne arrays asociativos por defecto, por ejemplo,
            al usar fetchAll() */
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            /* Asignar atributo para que PDO haga que los prepared statements sean reales a nivel
            de motor de base de datos. La separación entre estructura y datos ocurre en MySQL, no
            en PHP */
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        } catch (PDOException $e) {
            // Si la conexión falla, se guarda null. El controlador verificará si la conexión es válida
            $this->connection = null;
        } 
    }

    // Método público que retorna la conexión a la base de datos:
    public function getConnection(){
        return $this->connection;
    }

    // Método público que cierra la conexión a la base de datos:
    public function closeConnection(){
        /* En PDO, para cerrar la conexión simplemente se le asigna null al objeto. PHP
        libera los recursos automáticamente */
        $this->connection = null;
    }
}
?>