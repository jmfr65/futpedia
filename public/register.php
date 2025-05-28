<?php
// public/register.php

define('FUTPEDIA_ACCESS', true);
require_once __DIR__ . '/../includes/config.php'; // Carga $db, funciones de sesión, localización, etc.

// Si el usuario ya está logueado, redirigir
// if (is_logged_in()) {
//     redirect(BASE_URL . '/public/index.php'); 
// }

$page_title = __('register');
$page_description = __('Create a new account on Futpedia.');

$username = '';
$email = '';
$first_name = '';
$last_name = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');

    // Validaciones
    if (empty($username)) {
        $errors['username'] = __('Username is required.');
    }
    if (empty($email)) {
        $errors['email'] = __('Email is required.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = __('Invalid email format.');
    }
    if (empty($password)) {
        $errors['password'] = __('Password is required.');
    } elseif (mb_strlen($password) < 6) {
        $errors['password'] = __('Password must be at least 6 characters long.');
    }
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = __('Passwords do not match.');
    }

    if (empty($errors)) {
        if ($db && $db->getConnection()) {
            $db->query("SELECT id FROM users WHERE username = :username LIMIT 1");
            $db->bind(':username', $username);
            if ($db->single()) {
                $errors['username'] = __('Username already exists. Please choose another one.');
            }

            $db->query("SELECT id FROM users WHERE email = :email LIMIT 1");
            $db->bind(':email', $email);
            if ($db->single()) {
                $errors['email'] = __('Email already registered. Please use another one.');
            }
        } else {
            $errors['general'] = __('Database connection error. Please try again later.'); // Añadir esta clave si es necesario
        }
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        if ($db && $db->getConnection()) {
            try {
                $db->query("INSERT INTO users (username, email, password_hash, first_name, last_name, role, is_active) VALUES (:username, :email, :password_hash, :first_name, :last_name, 'user', TRUE)");
                $db->bind(':username', $username);
                $db->bind(':email', $email);
                $db->bind(':password_hash', $password_hash);
                $db->bind(':first_name', $first_name ?: null);
                $db->bind(':last_name', $last_name ?: null);
                
                if ($db->execute()) {
                    set_flash_message('success', __('Registration successful. You can now login.'));
                    redirect(BASE_URL . '/public/login.php'); // login.php aún no existe, esto dará 404
                } else {
                    $errors['general'] = __('Registration failed. Please try again.');
                    // --- DEBUG DB ERROR ---
                    if (DEBUG_MODE && $db) {
                         $errors['db_error'] = "DB Execute Error: " . ($db->getError() ?: 'No specific error message from DB class.');
                    }
                    // --- END DEBUG DB ERROR ---
                }
            } catch (PDOException $e) { // Específicamente PDOException
                error_log("Error en registro de usuario (PDOException): " . $e->getMessage());
                $errors['general'] = __('Failed to create user. Please contact support.');
                 // --- DEBUG PDO EXCEPTION ---
                if (DEBUG_MODE) {
                    $errors['pdo_exception'] = "PDOException: " . $e->getMessage();
                }
                // --- END DEBUG PDO EXCEPTION ---
            } catch (Exception $e) { // Otras excepciones generales
                error_log("Error en registro de usuario (Exception): " . $e->getMessage());
                $errors['general'] = __('Failed to create user. Please contact support.');
                // --- DEBUG GENERAL EXCEPTION ---
                if (DEBUG_MODE) {
                    $errors['exception'] = "Exception: " . $e->getMessage();
                }
                // --- END DEBUG GENERAL EXCEPTION ---
            }
        } else {
             $errors['general'] = __('Database connection error. Please try again later.');
        }
    }
}

require_once TEMPLATES_PATH . '/header.php';

// ---- BLOQUE DE DEBUG PRINCIPAL ----
if (DEBUG_MODE && $_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<div style='background-color: #ffffe0; border: 1px solid #ccc; padding: 10px; margin: 10px;'>";
    echo "<h3>DEBUG: Contenido de \$errors después de la validación y proceso:</h3><pre>";
    print_r($errors); // Usar print_r para mejor legibilidad de arrays
    echo "</pre>";
    echo "<hr><h3>DEBUG: Contenido de \$_POST:</h3><pre>";
    print_r($_POST);
    echo "</pre>";
    echo "</div>";
}
// ---- FIN DE BLOQUE DE DEBUG ----
?>

<div class="form-container" style="max-width: 500px; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px;">
    <h2><?php echo __('register'); ?></h2>

    <?php if (isset($errors['general'])): ?>
        <div class="flash-message error" style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border: 1px solid #f5c6cb;">
             <p><?php echo escape_html($errors['general']); ?></p>
        </div>
    <?php endif; ?>
    
    <?php display_flash_messages(); // Para mensajes flash (ej. de éxito si funcionara la redirección) ?>

    <form action="<?php echo escape_html(BASE_URL . '/public/register.php'); ?>" method="post" novalidate>
        <div style="margin-bottom: 15px;">
            <label for="username"><?php echo __('username'); ?>:</label>
            <input type="text" id="username" name="username" value="<?php echo escape_html($username); ?>" required style="width: 100%; padding: 8px; box-sizing: border-box;">
            <?php if (isset($errors['username'])): ?><small style="color: red; display: block; margin-top: 4px;"><?php echo escape_html($errors['username']); ?></small><?php endif; ?>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="email"><?php echo __('email'); ?>:</label>
            <input type="email" id="email" name="email" value="<?php echo escape_html($email); ?>" required style="width: 100%; padding: 8px; box-sizing: border-box;">
            <?php if (isset($errors['email'])): ?><small style="color: red; display: block; margin-top: 4px;"><?php echo escape_html($errors['email']); ?></small><?php endif; ?>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="password"><?php echo __('password'); ?>:</label>
            <input type="password" id="password" name="password" required style="width: 100%; padding: 8px; box-sizing: border-box;">
            <?php if (isset($errors['password'])): ?><small style="color: red; display: block; margin-top: 4px;"><?php echo escape_html($errors['password']); ?></small><?php endif; ?>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="confirm_password"><?php echo __('Confirm Password'); ?>:</label>
            <input type="password" id="confirm_password" name="confirm_password" required style="width: 100%; padding: 8px; box-sizing: border-box;">
            <?php if (isset($errors['confirm_password'])): ?><small style="color: red; display: block; margin-top: 4px;"><?php echo escape_html($errors['confirm_password']); ?></small><?php endif; ?>
        </div>
        
        <hr style="margin: 20px 0;">

        <div style="margin-bottom: 15px;">
            <label for="first_name"><?php echo __('First Name (Optional)'); ?>:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo escape_html($first_name); ?>" style="width: 100%; padding: 8px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="last_name"><?php echo __('Last Name (Optional)'); ?>:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo escape_html($last_name); ?>" style="width: 100%; padding: 8px; box-sizing: border-box;">
        </div>

        <button type="submit" style="background-color: #004d00; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;"><?php echo __('register'); ?></button>
    </form>
</div>

<?php
require_once TEMPLATES_PATH . '/footer.php';
?>