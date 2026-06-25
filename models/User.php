<?php
require_once 'Model.php'; // Incluir la clase padre que otorga el constructor de la conexión

class User extends Model {
    // Ya no se necesita declarar $connection ni el constructor porque están heredados de Model

    // Se pueden escribir los métodos directamente

    // Refactorizar todos los métodos con PDO

    // Crear el método signUp
    // Al poner ': array', se le asegura a PHP que el resultado final será estrictamente un arreglo.
    /** @param int[] $rolesArray */
    /* Al poner '@param int[] $rolesArray' se indica que el arreglo contiene únicamente IDs numéricas de 
    los roles */
    public function signUp(string $userName, string $userEmail, string $userPassword, string $confirmPassword, array $rolesArray = [2]): array {
        // 1. Sanitizar $user_name
        $userName = trim($userName);

        // 2. Validar contraseñas
        if($userPassword !== $confirmPassword)
        {
            return ['success' => false, 'errorMessage' => "<h1 id='error'>Las contraseñas no coinciden.</h1>"];
        }

        // Sanitizar $userEmail y verificar que tenga un formato válido
        $userEmail = trim($userEmail);

        if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'errorMessage' => 'El formato de correo electrónico no es válido'];
        }

        // 3. Hashear contraseña
        $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);

        // OBLIGAR a que todos los elementos de $rolesArray sean números enteros
        foreach ($rolesArray as $roleId) {
            if (!is_int($roleId)) {
                // Si algún elemento del array no es un número entero, detiene el programa inmediatamente lanzando un error
                return ['success' => false, 'errorMessage' => 'El array de roles contiene un valor que no es un número entero'];
            }
        }
        
        try {
            // 4. TRANSACCIÓN
            $this->connection->beginTransaction();
            /* Se empieza una transacción porque al insertar un usuario, también obligatoriamente se le debe insertar un rol, 
            por lo tanto, hay inserciones en dos tablas distintas, se usa una transacción para garantizar que haya atomicidad 
            (principio informático de "todo o nada") y evitar que hayan datos huérfanos. Se empieza en un bloque try-catch */ 

            // 6. INSERTAR USUARIO 
            $insertUserStmt = $this->connection->prepare(
                "INSERT INTO users (user_name, user_email, user_password) VALUES (?, ?, ?)"
            );

            $insertUserStmt->execute([$userName, $userEmail, $hashedPassword]);

            // 7. Obtener el ID del usuario registrado
            $userId = $this->connection->lastInsertId();

            // INSERTAR roles (usando un loop)
            // 8. Insertar rol al usuario recientemente registrado
            $insertUserRoleStmt = $this->connection->prepare(
                "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)"
            );

            // Bucle foreach para asignarle al usuario recién registrado su rol correspondiente
            foreach ($rolesArray as $roleId) {
                $insertUserRoleStmt->execute([$userId, $roleId]);
            }

            // 9. Commit para indicar que todo está correcto
            $this->connection->commit();

            return ['success' => true];

        } catch (Exception $e){
            // 10. Rollback para indicar que algo falló (primero verificar que haya una transacción activa)
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }
            return ['success' => false, 'errorMessage' => $e->getMessage()];
        }

    }
 
    // Crear el método logIn()
    public function logIn(string $userIdentifier, string $unhashedPassword): array {
        try {
            // Query con JOIN
            $logInUserStmt = $this->connection->prepare(
                "SELECT users.user_id, users.user_name, users.user_password, user_roles.role_id 
                 FROM users 
                 INNER JOIN user_roles ON users.user_id = user_roles.user_id 
                 WHERE users.user_name = ? OR users.user_email = ?"
            );
            
            $logInUserStmt->execute([$userIdentifier, $userIdentifier]);

            // Obtener los datos del usuario provenientes de la base de datos
            $resultsDatabase = $logInUserStmt->fetchAll();

            // Inicializar como null (que más adelante será un array) la información del usuario que no sean sus roles
            $userInfo = null; 
            // Crear un array vacío fuera del loop para guardar la información de roles del usuario, ya que puede tener varios
            $rolesArray = []; 

            foreach ($resultsDatabase as $row) {
                /* // Añadir la información (sin incluir los roles aún) del usuario en la primera iteración únicamente, 
                guardar userName, userEmail y userPassword */
                if ($userInfo === null) {
                    $userInfo = [
                        'userId' => $row['user_id'],
                        'userName' => $row['user_name'],
                        'userPassword' => $row['user_password']
                    ];
                }

                // Ahora, en cada iteración: agregar el rol
                $rolesArray[] = $row['role_id'];
            }

            // Validar que se encontró el usuario en la database
            if($userInfo === null){
                return [
                    'success' => false,
                    'errorMessage' => 'Nombre de usuario/email no encontrado en la base de datos'
                ];
            }

            // Verificar passsword
            if(!password_verify($unhashedPassword, $userInfo['userPassword'])){
                return [
                    'success' => false,
                    'errorMessage' => 'Contraseña incorrecta.'
                ];
            }

            // Sin errores
            return [
                'success' => true,
                'userInfo' => [
                    'userId' => $userInfo['userId'],
                    'userName' => $userInfo['userName'],
                    'rolesArray' => $rolesArray
                ]
            ];
        } catch (Exception $e) {
            return ['success' => false, 'errorMessage' => 'Error en la base de datos: ' . $e->getMessage()];
        }
        
    }
}
?>