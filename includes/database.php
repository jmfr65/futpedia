<?php
// includes/database.php

// Asegurarnos de que config.php (que carga db_config.php) ya ha sido incluido.
// Si no, podríamos incluirlo aquí, pero es mejor práctica que el punto de entrada principal lo haga.
// O verificar si las constantes ya están definidas antes de intentar usarlas.
// if (!defined('DB_HOST')) {
//     // Esto podría ser un punto problemático si este archivo se incluye antes que config.php
//     // o si db_config.php falta.
//     if (file_exists(__DIR__ . '/config.php')) {
//         require_once __DIR__ . '/config.php';
//     } else {
//         // Situación crítica si config.php tampoco está.
//          die("Error crítico: No se pudo cargar config.php ni encontrar constantes de BD.");
//     }
// }

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;
    private $stmt;

    public function __construct() {
        // Cargar credenciales solo si están definidas (desde db_config.php via config.php)
        if (defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASS')) {
            $this->host = DB_HOST;
            $this->db_name = DB_NAME;
            $this->username = DB_USER;
            $this->password = DB_PASS;
        } else {
            // Manejar el caso donde las constantes de BD no están definidas
            $errorMessage = "Error de configuración de base de datos: Las constantes DB_HOST, DB_NAME, DB_USER, DB_PASS no están definidas. Asegúrate de que includes/db_config.php exista y sea correcto, y que includes/config.php se cargue primero.";
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                die($errorMessage);
            } else {
                error_log($errorMessage); // Loguear el error en producción
                die("Error de conexión. Por favor, inténtalo más tarde."); // Mensaje genérico para producción
            }
        }

        // DSN (Data Source Name) para PDO
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_PERSISTENT => true, // Conexiones persistentes (opcional, puede mejorar rendimiento)
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lanzar excepciones en errores
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Devolver resultados como arrays asociativos
            PDO::ATTR_EMULATE_PREPARES => false, // Usar preparaciones nativas (más seguro)
        ];

        try {
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            $errorMessage = "Error de conexión a la base de datos: " . $e->getMessage();
            // En modo debug, muestra el error detallado. En producción, un mensaje genérico.
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                die($errorMessage);
            } else {
                error_log("Error de conexión PDO: " . $e->getMessage()); // Loguear el error
                die("Error al conectar con la base de datos. Por favor, contacta al administrador.");
            }
        }
    }

    // Método para obtener la conexión PDO (si se necesita externamente, aunque es mejor encapsular)
    public function getConnection() {
        return $this->conn;
    }

    // Preparar la consulta
    public function query($sql) {
        try {
            $this->stmt = $this->conn->prepare($sql);
        } catch (PDOException $e) {
            $this->handleQueryError($e, $sql);
        }
    }

    // Vincular valores (bind)
    public function bind($param, $value, $type = null) {
        if (is_null($this->stmt)) {
             // Esto podría ocurrir si query() falló y no se manejó o si bind() se llama antes de query()
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                die("Error: Intento de bind en una declaración no preparada. ¿Llamaste a query() primero?");
            } else {
                error_log("Error: Intento de bind en una declaración no preparada.");
                // Podrías lanzar una excepción aquí también
                throw new Exception("Error interno del servidor al preparar la consulta.");
            }
        }
        try {
            if (is_null($type)) {
                switch (true) {
                    case is_int($value):
                        $type = PDO::PARAM_INT;
                        break;
                    case is_bool($value):
                        $type = PDO::PARAM_BOOL;
                        break;
                    case is_null($value):
                        $type = PDO::PARAM_NULL;
                        break;
                    default:
                        $type = PDO::PARAM_STR;
                }
            }
            $this->stmt->bindValue($param, $value, $type);
        } catch (PDOException $e) {
            $this->handleQueryError($e, "binding parameter " . $param);
        }
    }

    // Ejecutar la consulta preparada
    public function execute() {
        try {
            return $this->stmt->execute();
        } catch (PDOException $e) {
            $this->handleQueryError($e, "executing statement");
        }
    }

    // Obtener todos los resultados como un array de objetos (o asociativo según ATTR_DEFAULT_FETCH_MODE)
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    // Obtener un único resultado
    public function single() {
        $this->execute();
        return $this->stmt->fetch();
    }

    // Obtener el número de filas afectadas
    public function rowCount() {
        if ($this->stmt) {
            return $this->stmt->rowCount();
        }
        return 0; // O manejar como error si stmt no está inicializado
    }

    // Obtener el ID del último registro insertado
    public function lastInsertId() {
        try {
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            // Esto puede fallar si el driver no lo soporta o si la última consulta no fue un INSERT
            // o si la tabla no tiene auto-incremento.
            $this->handleQueryError($e, "getting lastInsertId");
        }
    }

    // Transacciones
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    public function endTransaction() {
        return $this->conn->commit();
    }

    public function cancelTransaction() {
        return $this->conn->rollBack();
    }

    // Manejador de errores de consulta centralizado
    private function handleQueryError(PDOException $e, $context = "a database operation") {
        $errorMessage = "Error durante " . $context . ": " . $e->getMessage();
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            // Podríamos añadir más detalles aquí, como el SQL si estuviera disponible y fuera seguro mostrarlo.
            // Por ejemplo, si $this->stmt->queryString estuviera disponible y fuera el SQL original.
            // $sql = isset($this->stmt) && property_exists($this->stmt, 'queryString') ? $this->stmt->queryString : "No SQL disponible";
            // die($errorMessage . " | SQL: " . $sql);
            die($errorMessage);

        } else {
            error_log("Error PDO: " . $errorMessage);
            // No relanzar la excepción directamente al usuario en producción si contiene info sensible.
            // Considera lanzar una excepción más genérica o simplemente die().
            die("Ocurrió un error con la base de datos. Por favor, inténtalo de nuevo más tarde.");
        }
    }
}
?>