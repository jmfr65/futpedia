<?php
// includes/localization.php

if (!defined('FUTPEDIA_ACCESS') || !FUTPEDIA_ACCESS) {
    die('Acceso directo no permitido a localization.php');
}

if (!defined('DEFAULT_LANG')) {
    define('DEFAULT_LANG', 'es');
}

if (!defined('SUPPORTED_LANGS')) {
    define('SUPPORTED_LANGS', ['es', 'en', 'fr', 'pt']);
}

global $translations;
$translations = [];

global $current_lang;
$current_lang = DEFAULT_LANG;

function set_current_language(): void {
    global $current_lang;
    $lang_to_set = DEFAULT_LANG; // Empezar con el default

    // 1. Desde el parámetro GET (si el usuario acaba de seleccionar un idioma)
    if (isset($_GET['lang']) && in_array($_GET['lang'], SUPPORTED_LANGS)) {
        $lang_to_set = $_GET['lang'];
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['current_lang'] = $lang_to_set; // Guardar en sesión para futuras peticiones
        }
    }
    // 2. Si no hay GET, desde la sesión (si el usuario ha elegido un idioma previamente)
    elseif (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['current_lang'])) {
        if (in_array($_SESSION['current_lang'], SUPPORTED_LANGS)) {
            $lang_to_set = $_SESSION['current_lang'];
        }
    }
    // 3. (Opcional) Desde las preferencias del navegador (HTTP_ACCEPT_LANGUAGE)
    // elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) { ... }

    // Establecer el idioma y cargar el archivo
    $current_lang = $lang_to_set;
    load_language_file($current_lang);
}

function load_language_file(string $lang): void {
    global $translations;
    $translations = []; 

    if (!in_array($lang, SUPPORTED_LANGS)) {
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            error_log("Idioma no soportado: $lang. Usando idioma por defecto.");
        }
        $lang = DEFAULT_LANG;
    }

    $lang_file_path = LANGUAGES_PATH . '/' . $lang . '.php'; // Usar la constante LANGUAGES_PATH

    if (file_exists($lang_file_path)) {
        $lang_array = require $lang_file_path;
        if (is_array($lang_array)) {
            $translations = $lang_array;
        } elseif (defined('DEBUG_MODE') && DEBUG_MODE) {
            error_log("El archivo de idioma $lang_file_path no devolvió un array.");
        }
    } elseif (defined('DEBUG_MODE') && DEBUG_MODE) {
        error_log("Archivo de idioma no encontrado: $lang_file_path");
        if ($lang !== DEFAULT_LANG) {
            load_language_file(DEFAULT_LANG);
        }
    }
}

function __(string $key, array $replacements = []): string {
    global $translations, $current_lang;
    if (empty($translations) && $current_lang) {
        load_language_file($current_lang);
    }
    
    $translated_string = $key; 

    if (isset($translations[$key])) {
        $translated_string = $translations[$key];
    } elseif (defined('DEBUG_MODE') && DEBUG_MODE) {
        // error_log("Clave de traducción faltante para '$current_lang': $key");
    }

    if (!empty($replacements) && is_string($translated_string)) {
        foreach ($replacements as $placeholder => $value) {
            $translated_string = str_replace('%' . $placeholder . '%', escape_html((string)$value), $translated_string);
        }
    }
    return $translated_string;
}

set_current_language();
?>