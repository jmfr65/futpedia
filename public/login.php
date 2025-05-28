<?php
// public/login.php

define('FUTPEDIA_ACCESS', true);
require_once __DIR__ . '/../includes/config.php'; // Carga $db, funciones de sesión, localización, etc.

// Si el usuario ya está logueado, redirigir a la página principal (o dashboard si existiera)
if (is_logged_in()) {
    redirect(BASE_URL . '/public/index.php'); 
    exit;
}

$page_title = __('Login');
$page_description = __('Login to your Futpedia account.');

$identifier = ''; // Puede ser username o email
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validaciones básicas
    if (empty($identifier)) {
        $errors['identifier'] = __('Username or Email is required.');
    }
    if (empty($password)) {
        $errors['password'] = __('Password is required.');
    }

    if (empty($errors)) {
        if ($db && $db->getConnection()) {
            try {
                // Intentar encontrar al usuario por username o email usando diferentes placeholders
                $db->query("SELECT id, username, email, password_hash, role, is_active FROM users WHERE username = :username_identifier OR email = :email_identifier LIMIT 1");
                $db->bind(':username_identifier', $identifier);
                $db->bind(':email_identifier', $identifier);
                
                $user = $db->single(); // El error HY093 parece ocurrir aquí o dentro de single()

                if ($user) {
                    if (password_verify($password, $user['password_hash'])) {
                        if ($user['is_active']) {
                            session_regenerate_id(true);

                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['username'] = $user['username'];
                            $_SESSION['user_role'] = $user['role']; 

                            // --- TEMPORALMENTE COMENTADO DEBIDO A COLUMNA FALTANTE ---
                            /*
                            $db->query("UPDATE users SET last_login_at = CURRENT_TIMESTAMP WHERE id = :id");
                            $db->bind(':id', $user['id']);
                            $db->execute();
                            */
                            // --- FIN DE BLOQUE COMENTADO ---

                            set_flash_message('success', __('Login successful. Welcome back, %username%!', ['username' => escape_html($user['username'])]));
                            redirect(BASE_URL . '/public/index.php'); 
                            exit;
                        } else {
                            $errors['general'] = __('Your account is not active. Please contact support.');
                        }
                    } else {
                        $errors['general'] = __('Invalid username/email or password.');
                    }
                } else {
                    // Si $user es false DESPUÉS de que la consulta SE EJECUTÓ SIN ERRORES PDO,
                    // entonces el usuario no fue encontrado.
                    // Si $user es false debido a una PDOException, el bloque catch lo manejará.
                    if (empty($errors['pdo_exception'])) { // Solo mostrar si no hay ya una excepción PDO
                         $errors['general'] = __('Invalid username/email or password.');
                    }
                }
            } catch (PDOException $e) {
                error_log("Error en login (PDOException): " . $e->getMessage() . " | SQL: " . ($db->getLastSql() ?: 'No SQL available'));
                $errors['general'] = __('An error occurred during login. Please try again later.');
                if (DEBUG_MODE) {
                    $errors['pdo_exception'] = "PDOException: " . $e->getMessage();
                    $errors['last_sql_debug'] = "Last SQL (Debug): " . ($db->getLastSql() ?: 'No SQL available');
                }
            }
        } else {
            $errors['general'] = __('Database connection error. Please try again later.');
        }
    }
}

require_once TEMPLATES_PATH . '/header.php';

// ---- BLOQUE DE DEBUG (si es necesario, similar a register.php) ----
if (DEBUG_MODE && $_SERVER['REQUEST_METHOD'] === 'POST' && !empty($errors)) {
    echo "<div style='background-color: #ffffe0; border: 1px solid #ccc; padding: 10px; margin: 10px;'>";
    echo "<h3>DEBUG: Contenido de \$errors después del intento de login:</h3><pre>";
    print_r($errors);
    echo "</pre>";
    echo "<hr><h3>DEBUG: Contenido de \$_POST:</h3><pre>";
    print_r($_POST);
    echo "</pre>";
    echo "</div>";
}
// ---- FIN DE BLOQUE DE DEBUG ----
?>

<div class="form-container" style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; background-color: #f9f9f9;">
    <h2 style="text-align: center; margin-bottom: 20px;"><?php echo __('Login'); ?></h2>

    <?php display_flash_messages(); ?>

    <?php if (isset($errors['general'])): ?>
        <div class="flash-message error" style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border: 1px solid #f5c6cb; border-radius: 4px;">
             <p><?php echo escape_html($errors['general']); ?></p>
             <?php if (isset($errors['pdo_exception'])): ?>
                <p style="font-size: 0.9em; margin-top: 5px;"><strong>Detalle técnico:</strong> <?php echo escape_html($errors['pdo_exception']); ?></p>
             <?php endif; ?>
             <?php if (isset($errors['last_sql_debug'])): ?>
                <p style="font-size: 0.9em; margin-top: 5px;"><strong>Última SQL:</strong> <?php echo escape_html($errors['last_sql_debug']); ?></p>
             <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <form action="<?php echo escape_html(BASE_URL . '/public/login.php'); ?>" method="post" novalidate>
        <div style="margin-bottom: 15px;">
            <label for="identifier" style="display: block; margin-bottom: 5px;"><?php echo __('Username or Email'); ?>:</label>
            <input type="text" id="identifier" name="identifier" value="<?php echo escape_html($identifier); ?>" required style="width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;">
            <?php if (isset($errors['identifier'])): ?><small style="color: red; display: block; margin-top: 4px;"><?php echo escape_html($errors['identifier']); ?></small><?php endif; ?>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="password" style="display: block; margin-bottom: 5px;"><?php echo __('Password'); ?>:</label>
            <input type="password" id="password" name="password" required style="width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;">
            <?php if (isset($errors['password'])): ?><small style="color: red; display: block; margin-top: 4px;"><?php echo escape_html($errors['password']); ?></small><?php endif; ?>
        </div>
        
        <button type="submit" style="background-color: #004d00; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px;"><?php echo __('Login'); ?></button>
    </form>
    
    <p style="text-align: center; margin-top: 20px;">
        <?php echo __("Don't have an account?"); ?> <a href="<?php echo BASE_URL . '/public/register.php'; ?>"><?php echo __('Register here'); ?></a>
    </p>
</div>

<?php
require_once TEMPLATES_PATH . '/footer.php';
?>