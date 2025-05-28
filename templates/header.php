<?php
// templates/header.php

if (defined('FUTPEDIA_ACCESS') && !FUTPEDIA_ACCESS && php_sapi_name() !== 'cli' && (!defined('DEBUG_MODE') || !DEBUG_MODE) ) {
    die('Acceso directo no permitido a header.php');
}

// $current_lang es una variable global establecida por localization.php, incluido en config.php
global $current_lang; 

if (!isset($page_title) || empty($page_title)) {
    // Usamos la función de traducción para el título por defecto si es necesario
    $page_title = function_exists('__') ? __('site_name') : (defined('SITE_NAME') ? SITE_NAME : "Futpedia");
}

if (!isset($page_description) || empty($page_description)) {
    $page_description = "Información y estadísticas de fútbol."; // Podría traducirse también
}

?>
<!DOCTYPE html>
<html lang="<?php echo escape_html($current_lang ?? 'es'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo escape_html($page_description); ?>">
    <title><?php echo escape_html($page_title); ?> - <?php echo function_exists('__') ? escape_html(__('site_name')) : (defined('SITE_NAME') ? escape_html(SITE_NAME) : 'Futpedia'); ?></title>
    
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; color: #333; }
        .main-header { background-color: #004d00; color: white; padding: 1em; text-align: center; }
        .main-header a { color: white; text-decoration: none; margin: 0 10px; }
        .main-nav ul { list-style-type: none; padding: 0; margin: 0; text-align: center; background-color: #333; }
        .main-nav ul li { display: inline; }
        .main-nav ul li a { display: inline-block; padding: 10px 15px; color: white; text-decoration: none; }
        .main-nav ul li a:hover { background-color: #555; }
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; background-color: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .main-footer { text-align: center; padding: 1em; margin-top: 20px; background-color: #333; color: white; }
        .flash-message { padding: 10px; margin-bottom: 15px; border-radius: 4px; font-weight: bold; }
        .flash-message.success { border: 1px solid #4CAF50; color: #388E3C; background-color: #e8f5e9; }
        .flash-message.error { border: 1px solid #f44336; color: #D32F2F; background-color: #ffebee; }
    </style>
</head>
<body>

<header class="main-header">
    <h1><a href="<?php echo defined('BASE_URL') ? escape_html(BASE_URL) : '/'; ?>"><?php echo function_exists('__') ? escape_html(__('site_name')) : 'Futpedia'; ?></a></h1>
    <nav class="main-nav">
        <ul>
            <li><a href="<?php echo defined('BASE_URL') ? escape_html(BASE_URL) : '/'; ?>"><?php echo function_exists('__') ? __('home') : 'Inicio'; ?></a></li>
            <?php if (false): // Reemplazar 'false' con una función como is_logged_in() ?>
                <li><a href="#"><?php echo function_exists('__') ? __('my_profile') : 'Mi Perfil'; ?></a></li>
                <li><a href="#"><?php echo function_exists('__') ? __('logout') : 'Cerrar Sesión'; ?></a></li>
            <?php else: ?>
                <li><a href="#"><?php echo function_exists('__') ? __('login') : 'Login'; ?></a></li>
                <li><a href="#"><?php echo function_exists('__') ? __('register') : 'Registro'; ?></a></li>
            <?php endif; ?>
            <?php if (false): // Reemplazar 'false' con una función como is_admin() ?>
                <li><a href="#"><?php echo function_exists('__') ? __('admin_panel') : 'Admin Panel'; ?></a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main class="container">
    <?php
    if (function_exists('display_flash_messages')) {
        display_flash_messages();
    }
    ?>