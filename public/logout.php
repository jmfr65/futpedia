<?php
// public/logout.php

define('FUTPEDIA_ACCESS', true);
require_once __DIR__ . '/../includes/config.php'; // Para BASE_URL, funciones de sesión, etc.

// Destruir todas las variables de sesión.
$_SESSION = array();

// Si se desea destruir la sesión completamente, borre también la cookie de sesión.
// Nota: ¡Esto destruirá la sesión, y no solo los datos de la sesión!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión.
session_destroy();

// Establecer un mensaje flash (opcional)
// No podemos usar set_flash_message directamente aquí porque la sesión acaba de ser destruida.
// Si quisiéramos un mensaje, tendríamos que pasarlo por GET o manejarlo de otra forma.
// Por simplicidad, solo redirigimos.

// Redirigir a la página de login o a la página principal
redirect(BASE_URL . '/public/login.php?logged_out=true'); // Añadimos un parámetro por si queremos mostrar un mensaje
exit;
?>