<?php
// languages/en.php - English language file

if (defined('FUTPEDIA_ACCESS') && !FUTPEDIA_ACCESS && php_sapi_name() !== 'cli') {
    die('Direct access not allowed to language file.');
}

return [
    // General
    'site_name' => 'Futpedia',
    'toggle_navigation' => 'Toggle navigation',
    'home' => 'Home',
    'login' => 'Login',
    'register' => 'Register',
    'logout' => 'Logout',
    'my_profile' => 'My Profile',
    'admin_panel' => 'Admin Panel',
    'search' => 'Search',
    'go' => 'Go',
    'yes' => 'Yes',
    'no' => 'No',
    'save' => 'Save',
    'edit' => 'Edit',
    'delete' => 'Delete',
    'cancel' => 'Cancel',
    'error' => 'Error',
    'success' => 'Success',
    'page_not_found' => 'Page Not Found',
    'oops_error_occurred' => 'Oops! An error occurred.',
    'welcome_message' => 'Welcome to the Heart of Futpedia!',
    'current_datetime_label' => 'Current date and time (formatted):',
    'db_connection_ok' => 'The database connection appears to be configured and working correctly.',
    'db_connection_error' => 'Error: DB_HOST is defined, but the Database instance was not created or the connection failed.',
    'under_construction_title' => 'Welcome to Futpedia',
    'under_construction_message' => 'This is the main entry point of the application, now with a basic design.',
    'under_construction_info' => 'Soon you will see dynamic content about the world of football here.',


    // Header/footer specific (examples)
    'main_navigation' => 'Main Navigation',
    'copyright_notice' => '&copy; %year% %site_name%. All rights reserved.',

    // Forms (examples)
    'username' => 'Username',
    'password' => 'Password',
    'email' => 'Email address',
    'remember_me' => 'Remember me',

    // Flash messages (examples)
    'flash_config_loaded_successfully' => 'Configuration and session loaded successfully!',
    'flash_test_error_message' => 'This is a test error message.',

];