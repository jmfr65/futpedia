<?php
// includes/functions.php

// Asegurarnos de que config.php ya ha sido incluido si necesitamos BASE_URL u otras constantes.
// if (!defined('BASE_URL')) {
//     if (file_exists(__DIR__ . '/config.php')) {
//         require_once __DIR__ . '/config.php';
//     } else {
//         // Manejo de error si config.php no está y es necesario
//         die("Error crítico: config.php no encontrado y es necesario para functions.php");
//     }
// }

/**
 * Escapa HTML especial para prevenir XSS.
 * Es una envoltura simple alrededor de htmlspecialchars.
 *
 * @param string|null $string La cadena a escapar.
 * @return string La cadena escapada.
 */
function escape_html($string) {
    if ($string === null) {
        return '';
    }
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirige a otra URL.
 * Asegúrate de que no haya salida antes de llamar a esta función.
 *
 * @param string $url La URL a la que redirigir (puede ser relativa a BASE_URL o absoluta).
 * @return void
 */
function redirect($url) {
    $final_url = $url; // Por defecto, usar la URL tal cual si es absoluta o BASE_URL no está definida.

    if (defined('BASE_URL')) {
        // Si la URL no es absoluta, la prefijamos con BASE_URL
        if (!(strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0 || strpos($url, '//') === 0)) {
            // Asegurarse de que no haya doble barra si $url comienza con /
            $trimmed_url = ltrim($url, '/');
            $final_url = rtrim(BASE_URL, '/') . '/' . $trimmed_url;
        }
    } elseif (!(strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0 || strpos($url, '//') === 0)) {
        // BASE_URL no definida Y la URL es relativa: esto es un problema.
        error_log("Error de redirección: BASE_URL no definida y se intentó redirigir a una URL relativa: " . $url);
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            die("Error de redirección: BASE_URL no definida. Verifica tu config.php. URL solicitada: " . escape_html($url));
        }
        // En producción, podría ser mejor no hacer nada o redirigir a una página de error genérica.
        // Por ahora, simplemente no redirigimos si no podemos construir la URL completa.
        return;
    }

    header("Location: " . $final_url);
    exit; // Importante: detener la ejecución del script después de la redirección.
}

/**
 * Comprueba si un usuario está logueado.
 * (Esto es un placeholder, la lógica real dependerá de cómo manejes las sesiones/autenticación)
 *
 * @return bool True si el usuario está logueado, false en caso contrario.
 */
function is_logged_in() {
    // Ejemplo básico: Comprobar si existe una variable de sesión específica.
    // Asegúrate de que la sesión esté iniciada (config.php debería hacerlo)
    if (session_status() == PHP_SESSION_NONE && (!defined('HEADERS_SENT') || !HEADERS_SENT)) {
        // Este caso es problemático, las sesiones deben iniciarse antes.
        // config.php debería encargarse de session_start().
        // No iniciar sesión aquí para evitar "headers already sent".
        error_log("is_logged_in() llamada sin sesión activa y config.php no la inició o falló.");
        return false;
    }
    return isset($_SESSION['user_id']);
}

/**
 * Formatea una fecha/hora.
 *
 * @param string|null $datetime_str La cadena de fecha/hora (ej: de la BD).
 * @param string $format El formato deseado (ej: 'd/m/Y H:i:s').
 * @return string La fecha/hora formateada o un mensaje de error/string vacío si es inválida.
 */
function format_datetime($datetime_str, $format = 'd/m/Y H:i:s') {
    if (empty($datetime_str) || $datetime_str === '0000-00-00 00:00:00' || $datetime_str === null) {
        return 'N/A'; // O lo que prefieras para fechas vacías/inválidas
    }
    try {
        // Intentar crear el objeto DateTime. Si es solo una fecha, puede que no necesite la hora.
        // Si $datetime_str ya es un objeto DateTime, no hacer nada.
        if ($datetime_str instanceof DateTimeInterface) {
            $date = $datetime_str;
        } else {
            $date = new DateTime($datetime_str);
        }
        return $date->format($format);
    } catch (Exception $e) {
        // Loguear el error o devolver la cadena original o un mensaje de error
        error_log("Error al formatear fecha: " . $e->getMessage() . " | Fecha original: " . $datetime_str);
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            return "Error al formatear fecha: " . escape_html($datetime_str);
        }
        return 'Fecha inválida';
    }
}

// Puedes añadir más funciones útiles aquí a medida que las necesites:
// - Generar slugs para URLs amigables.
// - Validar entradas (emails, números, etc.), aunque la validación del lado del servidor es mejor en clases/modelos.
// - Funciones para interactuar con APIs externas.
// - Etc.

?>