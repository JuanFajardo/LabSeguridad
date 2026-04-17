<?php
/**
 * =====================================================
 * PÁGINA DE REGISTRO DE USUARIOS
 * =====================================================
 * 
 * Sistema de registro básico para el laboratorio.
 * 
 * VULNERABILIDADES PRESENTES:
 * - SQL Injection en el registro
 * - No validación de datos de entrada
 * - Contraseñas almacenadas en texto plano
 * - Sin verificación de correo
 * =====================================================
 */

require_once '../config/config.php';

redirectIfLoggedIn();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $nombre_completo = trim($_POST['nombre_completo'] ?? '');
    
    // Validación básica
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Por favor, complete todos los campos obligatorios.";
    } elseif ($password !== $password_confirm) {
        $error = "Las contraseñas no coinciden.";
    } elseif (strlen($password) < 4) {
        $error = "La contraseña debe tener al menos 4 caracteres.";
    } else {
        $conn = getConnection();
        
        /**
         * =====================================================
         * VULNERABILIDAD: SQL INJECTION
         * =====================================================
         * Los datos se insertan directamente sin sanitización.
         * Se podrían usar payloads como:
         * '; DROP TABLE usuarios; --
         * 
         * DEMOSTRACIÓN EDUCATIVA
         * =====================================================
         */
        $sql = "INSERT INTO usuarios (username, email, password, nombre_completo) 
                VALUES ('$username', '$email', '$password', '$nombre_completo')";
        
        try {
            if ($conn->query($sql) === TRUE) {
                $new_user_id = $conn->insert_id;
                $user_hash = md5($new_user_id);
                $conn->query("UPDATE usuarios SET user_hash = '$user_hash' WHERE id = $new_user_id");
                $success = "¡Registro exitoso! Ya puedes iniciar sesión.";
            } else {
                // Verificar si es error de duplicado
                if (strpos($conn->error, 'Duplicate') !== false) {
                    $error = "El usuario o email ya está registrado.";
                } else {
                    $error = "Error en el registro: " . $conn->error;
                }
            }
        } catch (Exception $e) {
            $error = "Error en el registro: " . $e->getMessage();
        }
        
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Laboratorio de Seguridad</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h1>📝 Registro de Usuario</h1>
            <p class="subtitle">Laboratorio de Seguridad Web</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
                <p class="text-center"><a href="login.php" class="btn btn-primary">Ir a Iniciar Sesión</a></p>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Usuario: *</label>
                        <input type="text" id="username" name="username" required
                               placeholder="Nombre de usuario" value="<?php echo $_POST['username'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email: *</label>
                        <input type="email" id="email" name="email" required
                               placeholder="correo@ejemplo.com" value="<?php echo $_POST['email'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="nombre_completo">Nombre Completo:</label>
                        <input type="text" id="nombre_completo" name="nombre_completo"
                               placeholder="Tu nombre completo" value="<?php echo $_POST['nombre_completo'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Contraseña: *</label>
                        <input type="password" id="password" name="password" required
                               placeholder="Mínimo 4 caracteres">
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirm">Confirmar Contraseña: *</label>
                        <input type="password" id="password_confirm" name="password_confirm" required
                               placeholder="Repite la contraseña">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Registrarse</button>
                </form>
            <?php endif; ?>
            
            <p class="register-link">
                ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
            </p>
        </div>
    </div>
</body>
</html>
