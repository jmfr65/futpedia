<?php
// Iniciar la sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Configuración de la Base de Datos (Credenciales sensibles)
// Estas se cargarán desde db_config.php (que está en .gitignore)
// Por ahora, podemos definir placeholders o dejarlas para cuando creemos db_config.php
// define('DB_HOST', 'localhost');
// define('DB_USER', 'tu_usuario_db');
// define('DB_PASS', 'tu_password_db');
// define('DB_NAME', 'futpedia_db');

// Cargar configuración de la base de datos si el archivo existe
if (file_exists(__DIR__ . '/db_config.php')) {
    require_once __DIR__ . '/db_config.php';
} else {
    // Podrías manejar un error aquí si db_config.php es estrictamente necesario desde el inicio
    // die("Error: El archivo de configuración de la base de datos (db_config.php) no se encuentra.");
}


// URLs y Rutas del Sitio
// Asegúrate de que no haya una barra inclinada (/) al final de las URLs/Rutas

// URL base del sitio (ej: http://localhost/futpedia_dev o https://archivomundial.online/app)
// Detectar automáticamente si es HTTP o HTTPS
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST']; // ej: localhost o archivomundial.online
// Para el desarrollo local, $_SERVER['REQUEST_URI'] podría ser /futpedia_dev/ o /futpedia_dev/index.php
// Para producción, podría ser /app/ o /app/index.php
// Necesitamos el directorio base del proyecto.
// Si el script está en /includes/, dirname($_SERVER['SCRIPT_NAME']) podría ser /futpedia_dev o /app
// SCRIPT_NAME puede no ser fiable en todas las configuraciones si se usan alias o reescrituras complejas.
// Una aproximación más robusta para el subdirectorio si el proyecto no está en la raíz del dominio:
$script_path = $_SERVER['SCRIPT_NAME']; // ej: /futpedia_dev/includes/config.php (si se accede directamente) o /futpedia_dev/index.php
$project_subdir = dirname(dirname($script_path)); // Sube dos niveles desde /includes/config.php para llegar a /futpedia_dev
$project_subdir = str_replace('\\', '/', $project_subdir); // Normalizar barras para Windows
$project_subdir = ($project_subdir == '/' || $project_subdir == '.') ? '' : $project_subdir; // Evitar doble barra si está en la raíz o es '.'

define('BASE_URL', rtrim($protocol . $host . $project_subdir, '/'));

// Ruta base en el servidor (ej: C:/wamp64/www/futpedia_dev o /home/user/public_html/app)
// __DIR__ es la carpeta 'includes', así que necesitamos el directorio padre.
define('BASE_PATH', dirname(__DIR__));


// Nombre del Sitio
define('SITE_NAME', 'Futpedia');

// Configuración del modo de depuración/desarrollo
// Poner en false en producción
define('DEBUG_MODE', true); // Cambiar a false para producción

// Configuración de errores
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
    // Aquí podrías configurar el registro de errores en un archivo para producción
    // ini_set('log_errors', 1);
    // ini_set('error_log', BASE_PATH . '/logs/php_errors.log');
}

// Otras configuraciones globales
// define('ITEMS_PER_PAGE', 10);
// define('DEFAULT_LANGUAGE', 'es');

// Aquí podrías añadir más configuraciones según las necesidades del proyecto.

?>