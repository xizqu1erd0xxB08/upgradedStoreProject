<?php                                                                                               
// REFACTORIZAR CONTROLLER A PDO 
session_start();

// Evitar caché del navegador (botón atrás)
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Llamar a los archivos que se necesitan para el funcionamiento del proceso
/* __DIR__ representa la ruta absoluta del archivo desde donde se llama, es decir, 'C:/xampp/htdocs/
PHP_con_Claude/upgradedStoreProject/controllers'. Al unirlo con la función 'dirname', lo único que 
hace es borrar la última palabra de esa ruta, es decir, el equivalente a 'subir una carpeta'. Entonces
dirname(__DIR__) se convierte en: 'C:/xampp/htdocs/PHP_con_Claude/upgradedStoreProject'. Y al unir 
todo, PHP lee esto: 'C:/xampp/htdocs/PHP_con_Claude/upgradedStoreProject/config.php'. Además la 
función 'dirname()' permite pasarle un segundo número para decirle cuántos niveles subir, evitando 
tener que anidar funciones, en el caso de 1 no es necesario ponerlo, pero si quisiera subir dos
niveles desde el archivo actual, debe ponerse el número 2 */
require_once dirname(__DIR__, 1) . '/config.php';
require_once dirname(__DIR__, 1) . '/models/Database.php';
require_once dirname(__DIR__, 1) . '/models/User.php';

// Validar que los datos del formulario hayan llegado por POST
if (!isset($_POST['userIdentifier']) || !isset($_POST['userPassword'])) {
    die("Datos del formulario no encontrados");
}

$userIdentifier = $_POST['userIdentifier']; // Puede ser userName o userEmail
$userPassword = $_POST['userPassword'];

// Crear la conexión
$database = new Database($hostName, $hostAdmin, $hostAdminPassword, $databaseName);
$connection = $database->getConnection();

// Ahora, con PDO, si la conexión falló el método 'getConnection()' retorna 'null'
if ($connection === null) {
    die("No se pudo conectar a la base de datos: " . $database->getConnectionError());
}

// Crear objeto $user
$user = new User($connection);

// Llamar al método logIn y guardar su resultado
$loginResult = $user->logIn($userIdentifier, $userPassword);

// Verificar que el inicio de sesión haya sido realizado correctamente
if (!$loginResult['success']){
    $database->closeConnection();
    die("Error: " . $loginResult['errorMessage']);
}

// Si todo salió correctamente, guardar datos en sesión
$_SESSION['userId'] = $loginResult['userInfo']['userId'];
$_SESSION['userName'] = $loginResult['userInfo']['userName'];
$_SESSION['rolesArray'] = $loginResult['userInfo']['rolesArray'];

// Cerrar conexión
$database->closeConnection();

/* Redirigir a dashboard, se usa ruta relativa porque header() le da instrucciones directamente
al navegador del usuario, no al servidor, el navegador no entiende rutas absolutas del disco
duro */
header("Location: ../views/dashboard.php");
exit();
?>