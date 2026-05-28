<?php
require_once 'Database.php';
class User {
    // Crear la propiedad $connection
    private $connection;

    // Crear constructor con $connection
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    // Crear el método signUp
    public function signUp($user_name, $password, $confirm_password, $roles_array)
    {
        // 1. Sanitizar $user_name
        $user_name = trim($user_name);

        // 2. Validar contraseñas
        if($password !== $confirm_password)
        {
            return ['success' => false, 'errorMessage' => 'Passwords do not match'];
        }

        // 3. Hashear contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 4. TRANSACCIÓN (nuevo concepto)
        mysqli_begin_transaction($this->connection);

        // 5. Usar MANEJO DE EXCEPCIONES (bloques try-catch (nuevo concepto))
        try {
            // 6. INSERTAR USUARIO (prepared statements)
            $insert_user_query = "INSERT INTO users (user_name, password) VALUES (?, ?)";
            $insert_user_stmt = mysqli_prepare($this->connection, $insert_user_query);
            mysqli_stmt_bind_param($insert_user_stmt, 'ss', $user_name, $hashed_password);

            // Validar inserción del usuario
            if(!mysqli_stmt_execute($insert_user_stmt) || mysqli_stmt_affected_rows($insert_user_stmt) <= 0){
                throw new Exception("Error inserting user: " . mysqli_stmt_error($insert_user_stmt));
            }

            // 7. Obtener el user_id
            $user_id = mysqli_insert_id($this->connection);
            mysqli_stmt_close($insert_user_stmt); // Cerrar el Statement de insert_user

            // INSERTAR roles (usando un loop)
            // 8. Insertar rol al usuario con Prepared Statements
            $insert_user_role_query = "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)";
            $insert_user_role_stmt = mysqli_prepare($this->connection, $insert_user_role_query);
            foreach($roles_array as $role_id){
                mysqli_stmt_bind_param($insert_user_role_stmt, 'ii', $user_id, $role_id);

                // Validar inserción del rol
                if(!mysqli_stmt_execute($insert_user_role_stmt) || mysqli_stmt_affected_rows($insert_user_role_stmt) <= 0){
                    throw new Exception("Error inserting role: " . mysqli_stmt_error($insert_user_role_stmt));
                }
            }
            
            mysqli_stmt_close($insert_user_role_stmt); // Cerrar el Statement de insert_user_role

            // 9. Commit para indicar que todo está correcto
            mysqli_commit($this->connection);
            return ['success' => true];
        } catch (Exception $error){
            // 10. Rollback para indicar que algo falló
            mysqli_rollback($this->connection);
            return ['success' => false, 'errorMessage' => $error->getMessage()];
        }
    }
 
    // Crear el método logIn()
    public function logIn($user_name, $password) // Solo 2 parámetros
    {
        // Query con JOIN
        $login_user_query = "SELECT users.user_id, users.user_name, users.password, user_roles.role_id 
                            FROM users 
                            INNER JOIN user_roles ON users.user_id = user_roles.user_id 
                            WHERE users.user_name = ?";
        
        // Preparar Statement
        $login_user_stmt = mysqli_prepare($this->connection, $login_user_query);

        // Bindear el Statement
        mysqli_stmt_bind_param($login_user_stmt, 's', $user_name);

        // Ejecutar el Statement
        $login_user_stmt_executed = mysqli_stmt_execute($login_user_stmt);

        // Validar ejecución
        if (!$login_user_stmt_executed) {
            mysqli_stmt_close($login_user_stmt);
            return ['success' => false, 
                    'errorMessage' => 'Error al ejecutar la consulta con stmt: ' . mysqli_stmt_error($login_user_stmt)
            ];
        }

        // Obtener resultado de la query
        $login_user_stmt_result = mysqli_stmt_get_result($login_user_stmt);

        $user_info = null; // Inicializar como null (que más adelante será un array) la información del usuario que no sean sus roles
        $roles_array = []; // Se crea un array vacío fuera del loop para guardar la información de roles del usuario, ya que puede tener varios

        // Crear loop while para guardar la información del usuario 
        while($row = mysqli_fetch_assoc($login_user_stmt_result)){
            // Añadir la información (sin incluir los roles aún) del usuario en la primera iteración únicamente, guardar user_id, user_name, password
            if ($user_info === null) {
                $user_info = [
                    'user_id' => $row['user_id'],
                    'user_name' => $row['user_name'],
                    'password' => $row['password']
                ];
            }

            // Ahora, en cada iteración: agregar el rol
            $roles_array[] = $row['role_id'];
        }

        mysqli_stmt_close($login_user_stmt); // Cerrar el Statement

        // Validar que se encontró el usuario en la database
        if($user_info === null){
            return [
                'success' => false,
                'errorMessage' => 'User not found.'
            ];
        }

        // Verificar passsword
        if(!password_verify($password, $user_info['password'])){
            return [
                'success' => false,
                'errorMessage' => 'Invalid credentials.'
            ];
        }

        // Todo correcto, retornar array exitoso
        return [
            'success' => true,
            'user' => [
                'user_id' => $user_info['user_id'],
                'user_name' => $user_info['user_name'],
                'roles' => $roles_array
            ]
        ];
    }
}
?>