<?php
// templates/footer.php

if (defined('FUTPEDIA_ACCESS') && !FUTPEDIA_ACCESS && php_sapi_name() !== 'cli' && (!defined('DEBUG_MODE') || !DEBUG_MODE) ) {
    die('Acceso directo no permitido a footer.php');
}

global $current_lang; 

$year = date('Y');
$site_name_footer = function_exists('__') ? __('site_name') : (defined('SITE_NAME') ? SITE_NAME : 'Futpedia');
$copyright_text = function_exists('__') ? __('copyright_notice', ['year' => $year, 'site_name' => $site_name_footer]) : "&copy; {$year} {$site_name_footer}. Todos los derechos reservados.";

$supported_languages_array = defined('SUPPORTED_LANGS') ? SUPPORTED_LANGS : [];
$num_supported_langs = count($supported_languages_array);
$lang_iterator = 0;

?>

</main> <!-- Cierre de .container iniciado en header.php -->

<footer class="main-footer">
    <p><?php echo $copyright_text; ?></p>
    <p>
        <!-- Selector de idioma (ejemplo básico) -->
        <?php if (!empty($supported_languages_array) && function_exists('__')): ?>
            <?php foreach ($supported_languages_array as $lang_code): ?>
                <?php $lang_iterator++; ?>
                <a href="?lang=<?php echo $lang_code; ?>" <?php echo ($current_lang === $lang_code) ? 'style="font-weight:bold;"' : ''; ?>>
                    <?php echo strtoupper($lang_code); ?>
                </a>
                <?php if ($lang_iterator < $num_supported_langs): // Añadir separador si no es el último idioma ?>
                    |
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </p>
</footer>

</body>
</html>