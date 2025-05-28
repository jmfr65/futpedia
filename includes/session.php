<?php
// includes/session.php

// Asegurarse de que este archivo no se accede directamente si no es incluido
if (!defined('FUTPEDIA_ACCESS') || !FUTPEDIA_ACCESS) {
    die('Acceso directo no permitido a session.php');
}

// La sesión ya DEBE estar iniciada en config.php ANTES de incluir este archivo.

/**
 * Establece un mensaje flash en la sesión.
 *
 * @param string $name Nombre del mensaje (ej. 'success_message', 'error_message')
 * @param string $message El mensaje a mostrar.
 */
function set_flash_message(string $name, string $message): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION[$name] = $message;
    } elseif (defined('DEBUG_MODE') && DEBUG_MODE) {
        // Esto podría ir a un log de errores en producción
        echo "<p style='color:orange;border:1px solid orange;padding:5px;'>Advertencia DEBUG (session.php): set_flash_message('$name') llamada pero la sesión NO está activa.</p>";
    }
}

/**
 * Obtiene y elimina un mensaje flash de la sesión.
 *
 * @param string $name Nombre del mensaje.
 * @return string|null El mensaje si existe, null si no.
 */
function get_flash_message(string $name): ?string {
    if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION[$name])) {
        $message = $_SESSION[$name];
        unset($_SESSION[$name]); // Clave para que solo se muestre una vez
        return $message;
    }
    return null;
}

/**
 * Muestra los mensajes flash (éxito y error) si existen.
 * Utiliza las funciones escape_html() y get_flash_message().
 */
function display_flash_messages(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            echo '<div class="flash-message error" style="padding: 10px; margin-bottom: 15px; border: 1px solid red; color: red; background-color: #ffe6e6;">Error DEBUG (session.php): display_flash_messages llamada pero la sesión NO está activa.</div>';
        }
        return;
    }

    $success_message = get_flash_message('success_message');
    if ($success_message) {
        // Usamos los estilos definidos en header.php o un CSS externo
        echo '<div class="flash-message success">' . escape_html($success_message) . '</div>';
    }

    $error_message = get_flash_message('error_message');
    if ($error_message) {
        echo '<div class="flash-message error">' . escape_html($error_message) . '</div>';
    }
}
?>