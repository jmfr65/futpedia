<?php
// languages/es.php - Archivo de idioma español

if (defined('FUTPEDIA_ACCESS') && !FUTPEDIA_ACCESS && php_sapi_name() !== 'cli') {
    die('Acceso directo no permitido a archivo de idioma.');
}

return [
    // Generales
    'site_name' => 'Futpedia',
    'toggle_navigation' => 'Alternar navegación',
    'home' => 'Inicio',
    'login' => 'Login',
    'register' => 'Registro',
    'logout' => 'Cerrar Sesión',
    'my_profile' => 'Mi Perfil',
    'admin_panel' => 'Panel de Admin',
    'search' => 'Buscar',
    'go' => 'Ir',
    'yes' => 'Sí',
    'no' => 'No',
    'save' => 'Guardar',
    'edit' => 'Editar',
    'delete' => 'Eliminar',
    'cancel' => 'Cancelar',
    'error' => 'Error',
    'success' => 'Éxito',
    'page_not_found' => 'Página no encontrada',
    'oops_error_occurred' => '¡Ups! Ha ocurrido un error.',
    'welcome_message' => '¡Bienvenido al Corazón de Futpedia!',
    'current_datetime_label' => 'Fecha y hora actual (formateada):',
    'db_connection_ok' => 'La conexión a la base de datos parece estar configurada y funcionando correctamente.',
    'db_connection_error' => 'Error: DB_HOST está definido, pero la instancia de Database no se creó o la conexión falló.',
    'under_construction_title' => 'Bienvenido a Futpedia',
    'under_construction_message' => 'Este es el punto de entrada principal de la aplicación, ahora con un diseño básico.',
    'under_construction_info' => 'Pronto aquí verás contenido dinámico sobre el mundo del fútbol.',

    // Específicos del header/footer (ejemplos)
    'main_navigation' => 'Navegación Principal',
    'copyright_notice' => '&copy; %year% %site_name%. Todos los derechos reservados.',

    // Formularios (ejemplos)
    'username' => 'Nombre de usuario',
    'password' => 'Contraseña',
    'email' => 'Correo electrónico',
    'remember_me' => 'Recuérdame',
    
    // Mensajes flash (ejemplos)
    'flash_config_loaded_successfully' => '¡Configuración y sesión cargadas correctamente!', // Ejemplo si quisiéramos traducir el mensaje de prueba anterior
    'flash_test_error_message' => 'Este es un mensaje de error de prueba.', // Ejemplo

  // --- Traducciones para la página de Registro (register.php) ---
    'Create a new account on Futpedia.' => 'Crea una nueva cuenta en Futpedia.',
    'Please correct the errors below:' => 'Por favor, corrige los errores indicados:',
    'Confirm Password' => 'Confirmar Contraseña',
    'First Name (Optional)' => 'Nombre (Opcional)',
    'Last Name (Optional)' => 'Apellido (Opcional)',
	
	  // --- Traducciones para validación y proceso de Registro ---
    'Registration successful. You can now login.' => 'Registro completado con éxito. Ahora puedes iniciar sesión.',
    'Registration failed. Please try again.' => 'El registro falló. Por favor, inténtalo de nuevo.',
    'Username is required.' => 'El nombre de usuario es obligatorio.',
    'Email is required.' => 'El correo electrónico es obligatorio.',
    'Invalid email format.' => 'Formato de correo electrónico no válido.',
    'Password is required.' => 'La contraseña es obligatoria.',
    'Password must be at least 6 characters long.' => 'La contraseña debe tener al menos 6 caracteres.',
    'Passwords do not match.' => 'Las contraseñas no coinciden.',
    'Username already exists. Please choose another one.' => 'El nombre de usuario ya existe. Por favor, elige otro.',
    'Email already registered. Please use another one.' => 'El correo electrónico ya está registrado. Por favor, utiliza otro.',
    'Failed to create user. Please contact support.' => 'No se pudo crear el usuario. Por favor, contacta con soporte.',

];