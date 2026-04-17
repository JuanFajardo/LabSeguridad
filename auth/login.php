<?php
/**
 * =====================================================
 * PÁGINA DE INICIO DE SESIÓN
 * =====================================================
 * 
 * Sistema de login básico para el laboratorio de seguridad.
 * 
 * VULNERABILIDADES PRESENTES:
 * - Inyección SQL en la consulta de autenticación
 * - Contraseñas en texto plano (ya en la BD)
 * - Sin protección contra fuerza bruta
 * - Sin rate limiting
 * - Mensajes de error detallados que revelan información
 * =====================================================
 */

require_once '../config/config.php';

redirectIfLoggedIn();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = "Por favor, complete todos los campos.";
    } else {
        $conn = getConnection();
        
        /**
         * =====================================================
         * VULNERABILIDAD: SQL INJECTION
         * =====================================================
         * La entrada del usuario se concatena directamente
         * en la consulta SQL sin ningún tipo de sanitización.
         * 
         * Un atacante puede usar:
         * - Username: ' OR '1'='1
         * - Password: ' OR '1'='1
         * 
         * Esto convertiría la consulta en:
         * SELECT * FROM usuarios WHERE username='' OR '1'='1' AND password='' OR '1'='1'
         * 
         * DEMOSTRACIÓN EDUCATIVA
         * =====================================================
         */
        $sql = "SELECT * FROM usuarios WHERE username = '$username' AND password = '$password'";
        
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Iniciar sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nombre_completo'] = $user['nombre_completo'];
            
            header("Location: ../index.php");
            exit;
        } else {
            /**
             * =====================================================
             * VULNERABILIDAD: Information Disclosure
             * =====================================================
             * El mensaje de error revela si el usuario existe
             * o no, permitiendo enumeración de usuarios.
             * =====================================================
             */
            $error = "Usuario o contraseña incorrectos.";
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
    <title>Iniciar Sesión - Laboratorio de Seguridad</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h1>🔐 Iniciar Sesión</h1>
            <p class="subtitle">Laboratorio de Seguridad Web</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Usuario:</label>
                    <input type="text" id="username" name="username" required 
                           placeholder="Ingrese su usuario">
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required
                           placeholder="Ingrese su contraseña">
                </div>
                
                <button type="submit" class="btn btn-primary">Ingresar</button>
            </form>
            
            <p class="register-link">
                ¿No tienes cuenta? <a href="register.php">Regístrate aquí</a>
            </p>
            
            <!-- Panel informativo para el laboratorio -->
            <div class="info-panel">
                <h3>📚 Datos de Prueba</h3>
                <p><strong>Usuario:</strong> admin | <strong>Contraseña:</strong> admin123</p>
                <p><strong>Usuario:</strong> juan_perez | <strong>Contraseña:</strong> juan123</p>
            </div>
        </div>
    </div>
</body>
</html>
