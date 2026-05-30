<?php 
// Clase Database mejorado con PDO 
class Database {
    /* Propiedad privada para la conexión porque solo Database necesita manejar la conexión. 
    Las clases hijas (Product, User, Sale) recibirán la conexión ya lista. 
    Al usar ?PDO se le dice a PHP que la propiedad puede contener un objeto PDO o ser null si
    la conexión llega a fallar en el bloque try-catch. Al poner = null; se le está asignando 
    un valor por defecto al arrancar. Como todavía no se ha ejecutado el constructor, la conexión 
    empieza vacía (null) */
    private ?PDO $connection = null;
    // Nueva propiedad para guardar el error técnico de error a la base de datos
    private ?string $connectionError = null; 

    /* Constructor que junta los parámetros para realizar la conexión a la base de datos (todos los 
    parámetros de configuración de la base de datos son estrictamente cadenas de texto, es decir, string) */ 
    public function __construct(string $hostName, string $hostAdmin, string $hostAdminPassword, string $databaseName) {
        // Construir el Data Source Name (DSN)
        $dsn = "mysql:host=$hostName;dbname=$databaseName;charset=utf8mb4";

        /* charset=utf8mb4 es importante porque soporta emojis y caracteres especiales, utf8 normal 
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
            $this->connectionError = $e->getMessage(); // Se guarda el mensaje de error
        } 
    }

    /* Método público que retorna la conexión a la base de datos.
    Declarar ': ?PDO' como tipo de retorno. Significa que este método devuelve objeto de conexión PDO 
    o devuelve 'null' si hubo error */
    public function getConnection(): ?PDO {
        return $this->connection;
    }

    // Método para que el controller pueda acceder al mensaje técnico de error a la base de datos
    public function getConnectionError(): ?string {
        return $this->connectionError;
    }

    /* Método público que cierra la conexión a la base de datos.
    Usar ': void' porque este método realiza una acción pero no retorna ningún valor, ': void' es el
    estándar de PHP para funciones que destruyen o limpian recursos sin devolver resultados */
    public function closeConnection(): void {
        /* En PDO, para cerrar la conexión simplemente se le asigna null al objeto. PHP
        libera los recursos automáticamente */
        $this->connection = null;
    }
}
?>