<?php
// public/index.php

define('FUTPEDIA_ACCESS', true);
require_once __DIR__ . '/../includes/config.php'; // Carga todo, incluyendo localización

$db = null;
if (defined('DB_HOST') && defined('DB_USER') && defined('DB_NAME')) {
    try {
        if (class_exists('Database')) {
            $db = new Database();
        } elseif (defined('DEBUG_MODE') && DEBUG_MODE) {
            echo "<p style='color:red;'>Error: La clase Database no fue encontrada.</p>";
        }
    } catch (Exception $e) {
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            echo "<p style='color:red;'>Error al instanciar la clase Database: " . escape_html($e->getMessage()) . "</p>";
        }
    }
} elseif (defined('DEBUG_MODE') && DEBUG_MODE) {
    echo "<p style='color:orange;'>Advertencia: Constantes de base de datos no definidas.</p>";
}

// $page_title se define ANTES de incluir header.php
// Usamos la función de traducción para el título
$page_title = __('under_construction_title'); // Traducido de 'Bienvenido a Futpedia'
$page_description = __('under_construction_info'); // Traducido

if (file_exists(TEMPLATES_PATH . '/header.php')) {
    require_once TEMPLATES_PATH . '/header.php';
} else {
    if (defined('DEBUG_MODE') && DEBUG_MODE) { echo "<p style='color:red;'>Error Crítico: templates/header.php no encontrado.</p>"; }
    echo "<!DOCTYPE html><html lang='".($current_lang ?? 'es')."'><head><meta charset='UTF-8'><title>" . escape_html($page_title) . "</title></head><body><div class='container'>";
}

?>

<!-- Contenido específico de la página de inicio -->
<div style="text-align: center; padding: 20px 0;">
    <h2><?php echo __('welcome_message'); ?></h2>
    <p><?php echo __('under_construction_message'); ?></p>
    <p><?php echo __('under_construction_info'); ?></p>

    <?php
    if (function_exists('format_datetime')) {
        echo "<p>" . __('current_datetime_label') . " " . format_datetime(date('Y-m-d H:i:s')) . "</p>";
    } else {
        echo "<p>" . __('current_datetime_label') . " " . date('d/m/Y H:i:s') . "</p>";
    }

    if ($db && $db->getConnection() && defined('DB_HOST')) {
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            echo "<p style='color:green;'>" . __('db_connection_ok') . "</p>";
        }
    } elseif (defined('DB_HOST')) {
         if (defined('DEBUG_MODE') && DEBUG_MODE && (!$db || !$db->getConnection())) {
            echo "<p style='color:red;'>" . __('db_connection_error') . "</p>";
         }
    }
    ?>
</div>
<!-- Fin del contenido específico de la página de inicio -->

<?php
if (file_exists(TEMPLATES_PATH . '/footer.php')) {
    // Antes de incluir el footer, podríamos traducir el copyright si está ahí
    // Por ahora, supongamos que el footer.php usa la función __() directamente
    require_once TEMPLATES_PATH . '/footer.php';
} else {
    if (defined('DEBUG_MODE') && DEBUG_MODE) { echo "<p style='color:red;'>Error Crítico: templates/footer.php no encontrado.</p>"; }
    echo "</div></body></html>";
}
?>