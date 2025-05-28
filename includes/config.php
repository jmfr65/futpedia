<?php
// includes/config.php

if (!defined('FUTPEDIA_ACCESS')) {
    define('FUTPEDIA_ACCESS', true);
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('DEBUG_MODE', true);

if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set('UTC');
}

define('SITE_NAME', 'Futpedia');

// --- INICIO DE LA SECCIÓN CORREGIDA Y MEJORADA PARA BASE_URL ---
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST']; // ej. localhost

// ROOT_PATH se define más abajo, pero lo necesitamos aquí.
// Si esta es la primera vez que se define, asegúrate de que sea la correcta.
// Si ya está definida antes en este script (lo cual no es el caso en tu versión anterior), estaría bien.
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__)); // Asume que config.php está en /includes, y __DIR__ es /includes, entonces dirname(__DIR__) es la raíz del proyecto.
}

// Normalizar las rutas de DOCUMENT_ROOT y ROOT_PATH para la comparación
$document_root_normalized = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/'); // ej. C:/wamp64/www
$project_root_normalized = rtrim(str_replace('\\', '/', ROOT_PATH), '/');           // ej. C:/wamp64/www/futpedia/futpedia_dev

// Calcular la parte de la ruta del proyecto que está más allá del document root
$base_path_on_server = '';
if (strpos($project_root_normalized, $document_root_normalized) === 0) {
    // Asegurarse de que $document_root_normalized no sea una cadena vacía antes de usar strlen
    $base_path_on_server = substr($project_root_normalized, strlen($document_root_normalized)); // Debería ser /futpedia/futpedia_dev
}

define('BASE_URL', $protocol . $host . $base_path_on_server); // Debería ser http://localhost/futpedia/futpedia_dev
// --- FIN DE LA SECCIÓN CORREGIDA Y MEJORADA PARA BASE_URL ---


// Las siguientes definiciones de rutas de directorios están bien y usan ROOT_PATH
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('LANGUAGES_PATH', ROOT_PATH . '/languages'); // Nueva constante para la ruta de idiomas

// ASSETS_PATH ahora también usará la BASE_URL correcta:
define('ASSETS_PATH', BASE_URL . '/assets');


// --- PARA DEPURAR BASE_URL (PUEDES DESCOMENTAR TEMPORALMENTE) ---
/*
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "ROOT_PATH: " . ROOT_PATH . "<br>";
echo "document_root_normalized: " . $document_root_normalized . "<br>";
echo "project_root_normalized: " . $project_root_normalized . "<br>";
echo "base_path_on_server: " . $base_path_on_server . "<br>";
echo "BASE_URL: " . BASE_URL . "<br>";
exit; // Detiene la ejecución para ver los valores. ¡RECUERDA COMENTAR O QUITAR ESTO DESPUÉS!
*/
// --- FIN DEPURACIÓN BASE_URL ---


if (file_exists(INCLUDES_PATH . '/db_config.php')) {
    require_once INCLUDES_PATH . '/db_config.php';
} else {
    die(DEBUG_MODE ? "Error Crítico: El archivo db_config.php no se encuentra." : "Error de configuración del sitio.");
}

if (file_exists(INCLUDES_PATH . '/functions.php')) {
    require_once INCLUDES_PATH . '/functions.php';
} else {
    die(DEBUG_MODE ? "Error Crítico: El archivo functions.php no se encuentra." : "Error de configuración del sitio.");
}

function session_start_secure(): bool {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return true;
    }
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params([
        'lifetime' => $cookieParams['lifetime'],
        'path' => $cookieParams['path'], // Debería ser la ruta base del proyecto si es posible
        'domain' => $cookieParams['domain'],
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'), // Más explícito
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    return session_start();
}

if (!session_start_secure()) {
    die(DEBUG_MODE ? "Error Crítico: No se pudo iniciar la sesión de forma segura." : "Error al iniciar la sesión.");
}

if (file_exists(INCLUDES_PATH . '/session.php')) {
    require_once INCLUDES_PATH . '/session.php';
} else {
    die(DEBUG_MODE ? "Error Crítico: El archivo session.php no se encuentra." : "Error de configuración del sitio.");
}

// --- Cargar sistema de localización ---
if (file_exists(INCLUDES_PATH . '/localization.php')) {
    require_once INCLUDES_PATH . '/localization.php';
    // La variable global $current_lang es establecida dentro de localization.php
} else {
    die(DEBUG_MODE ? "Error Crítico: El archivo localization.php no se encuentra." : "Error de configuración del sitio.");
    if (!function_exists('__')) {
        function __(string $key, array $replacements = []): string {
            $s = $key;
            if ($replacements) { foreach ($replacements as $k => $v) { $s = str_replace('%'.$k.'%', $v, $s); } }
            return $s;
        }
    }
    global $current_lang;
    $current_lang = 'es'; // Idioma por defecto fijo
}
// --- Fin sistema de localización ---

if (file_exists(INCLUDES_PATH . '/database.php')) {
    require_once INCLUDES_PATH . '/database.php';
} else {
    die(DEBUG_MODE ? "Error Crítico: El archivo database.php no se encuentra." : "Error de configuración del sitio.");
}

// Instanciar la clase Database para que $db esté disponible globalmente (si así lo deseas)
// o pasarla como dependencia donde se necesite.
// Por ahora, como register.php espera $db global, la instanciamos aquí.
if (class_exists('Database')) {
    try {
        $db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
        // Puedes añadir una prueba de conexión aquí si quieres
        // if (!$db->getConnection()) {
        //     die(DEBUG_MODE ? "Error Crítico: No se pudo establecer la conexión con la base de datos (verifique db_config.php)." : "Error de base de datos.");
        // }
    } catch (PDOException $e) {
        die(DEBUG_MODE ? "Error Crítico de PDO al conectar a la BD: " . $e->getMessage() : "Error de conexión con la base de datos.");
    }
} else {
    die(DEBUG_MODE ? "Error Crítico: La clase Database no está definida (verifique database.php)." : "Error de configuración del sistema.");
}

?>