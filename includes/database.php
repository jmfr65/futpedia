<?php
// includes/database.php

if (!defined('FUTPEDIA_ACCESS') || !FUTPEDIA_ACCESS) {
    die('Acceso directo no permitido a database.php');
}

// Asegurarse de que las constantes de la base de datos están definidas
if (!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PASS')) {
    $error_message = "Error Crítico: Las constantes de configuración de la base de datos (DB_HOST, DB_NAME, DB_USER, DB_PASS) no están definidas.";
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        die($error_message);
    } else {
        error_log($error_message); // Registrar el error
        die("Error de configuración del sistema. Por favor, contacte al administrador."); // Mensaje genérico
    }
}

class Database {
    private string $host = DB_HOST;
    private string $db_name = DB_NAME;
    private string $username = DB_USER;
    private string $password = DB_PASS;
    private string $charset = 'utf8mb4'; // Recomendado para soporte completo de Unicode

    private ?PDO $pdo = null;
    private ?PDOStatement $stmt = null;
    private ?string $error = null;

    /**
     * Constructor: Establece la conexión a la base de datos.
     */
    public function __construct() {
        $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
        $options = [
            PDO::ATTR_PERSISTENT => true,             // Conexiones persistentes (opcional, puede mejorar rendimiento)
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lanzar excepciones en errores PDO
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Modo de obtención por defecto: array asociativo
            PDO::ATTR_EMULATE_PREPARES => false,      // Deshabilitar emulación de preparadas para seguridad y rendimiento real
        ];

        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            $log_message = "Error de Conexión a la Base de Datos: " . $this->error;
            error_log($log_message); // Siempre registrar el error

            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                // En modo debug, podemos ser más explícitos o incluso detener la ejecución
                die($log_message . "<br>Por favor, verifica tus credenciales en includes/db_config.php y que el servidor MySQL esté corriendo.");
            } else {
                // En producción, un mensaje más genérico o manejarlo de otra forma
                // Podríamos lanzar una excepción personalizada aquí para ser capturada más arriba
                // Por ahora, el script que intente usar $db->getConnection() recibirá null.
            }
        }
    }

    /**
     * Devuelve la instancia de PDO.
     * @return PDO|null La instancia de PDO si la conexión fue exitosa, null en caso contrario.
     */
    public function getConnection(): ?PDO {
        return $this->pdo;
    }

    /**
     * Prepara una sentencia SQL.
     * @param string $sql La consulta SQL a preparar.
     */
    public function query(string $sql): void {
        if (!$this->pdo) {
            $this->error = "No hay conexión PDO disponible para preparar la consulta.";
            if (defined('DEBUG_MODE') && DEBUG_MODE) { echo "<p style='color:red;'>Error: " . $this->error . "</p>"; }
            return;
        }
        try {
            $this->stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            $this->error = "Error al preparar la consulta: " . $e->getMessage() . " | SQL: " . $sql;
            if (defined('DEBUG_MODE') && DEBUG_MODE) { echo "<p style='color:red;'>Error: " . $this->error . "</p>"; }
        }
    }

    /**
     * Vincula un valor a un parámetro nombrado o posicional en la sentencia SQL.
     * @param string|int $param El identificador del parámetro (ej. :nombre o 1).
     * @param mixed $value El valor a vincular.
     * @param int|null $type El tipo de dato PDO (PDO::PARAM_STR, PDO::PARAM_INT, etc.). Si es null, se determina automáticamente.
     */
    public function bind($param, $value, $type = null): void {
        if (!$this->stmt) {
            $this->error = "No hay sentencia preparada para vincular parámetros.";
             if (defined('DEBUG_MODE') && DEBUG_MODE) { echo "<p style='color:red;'>Error: " . $this->error . "</p>"; }
            return;
        }
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
    }

    /**
     * Ejecuta la sentencia preparada.
     * @return bool True en caso de éxito, False en caso de fallo.
     */
    public function execute(): bool {
        if (!$this->stmt) {
            $this->error = "No hay sentencia preparada para ejecutar.";
            if (defined('DEBUG_MODE') && DEBUG_MODE) { echo "<p style='color:red;'>Error: " . $this->error . "</p>"; }
            return false;
        }
        try {
            return $this->stmt->execute();
        } catch (PDOException $e) {
            $this->error = "Error al ejecutar la consulta: " . $e->getMessage() . " | SQL: " . $this->stmt->queryString;
            if (defined('DEBUG_MODE') && DEBUG_MODE) { echo "<p style='color:red;'>Error: " . $this->error . "</p>"; }
            return false;
        }
    }

    /**
     * Obtiene todos los resultados de la consulta como un array de arrays asociativos.
     * @return array|false Un array de resultados o false en caso de error.
     */
    public function resultSet(): array|false {
        if ($this->execute()) {
            return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return false;
    }

    /**
     * Obtiene un único resultado de la consulta como un array asociativo.
     * @return array|false Un array asociativo del resultado o false si no hay resultado o en caso de error.
     */
    public function single(): array|false {
        if ($this->execute()) {
            return $this->stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    /**
     * Obtiene el número de filas afectadas por la última sentencia DELETE, INSERT o UPDATE.
     * @return int El número de filas afectadas.
     */
    public function rowCount(): int {
        return $this->stmt ? $this->stmt->rowCount() : 0;
    }

    /**
     * Devuelve el ID de la última fila insertada o el valor de una secuencia.
     * @param string|null $name Nombre del objeto de secuencia del cual se debe devolver el ID (para algunos drivers).
     * @return string|false El ID de la última fila insertada, o false en caso de fallo.
     */
    public function lastInsertId(string $name = null): string|false {
        return $this->pdo ? $this->pdo->lastInsertId($name) : false;
    }

    /**
     * Inicia una transacción.
     * @return bool True en caso de éxito, False en caso de fallo.
     */
    public function beginTransaction(): bool {
        return $this->pdo ? $this->pdo->beginTransaction() : false;
    }

    /**
     * Confirma una transacción.
     * @return bool True en caso de éxito, False en caso de fallo.
     */
    public function commit(): bool {
        return $this->pdo ? $this->pdo->commit() : false;
    }

    /**
     * Revierte una transacción.
     * @return bool True en caso de éxito, False en caso de fallo.
     */
    public function rollBack(): bool {
        return $this->pdo ? $this->pdo->rollBack() : false;
    }

    /**
     * Devuelve el último mensaje de error.
     * @return string|null El mensaje de error, o null si no hay error.
     */
    public function getError(): ?string {
        return $this->error;
    }

    /**
     * Método de ayuda para debug: muestra información sobre la última consulta preparada.
     * Solo para uso en desarrollo.
     */
    public function debugDumpParams(): void {
        if ($this->stmt && defined('DEBUG_MODE') && DEBUG_MODE) {
            echo "<pre>Debug PDO Statement:\n";
            $this->stmt->debugDumpParams();
            echo "</pre>";
        }
    }
}
?>