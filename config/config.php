<?php
/**
 * =====================================================
 * CONFIGURACIÓN DEL LABORATORIO DE SEGURIDAD WEB
 * =====================================================
 * 
 * Este archivo contiene la configuración de la aplicación.
 * 
 * VULNERABILIDADES CONFIGURADAS:
 * - Sesiones sin HttpOnly ni Secure flags
 * - Sin protección CSRF
 * - Sin headers de seguridad
 * 
 * NOTA: Todo esto es INTencionado para fines educativos.
 * =====================================================
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP por defecto no tiene contraseña
define('DB_NAME', 'security_lab');

// Rutas del sistema
define('BASE_PATH', '/security_lab/');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', BASE_PATH . 'uploads/');

// =====================================================
// VULNERABILIDAD: Configuración insegura de sesiones
// =====================================================
// En producción, usar:
// ini_set('session.cookie_httponly', 1);
// ini_set('session.cookie_secure', 1);
// ini_set('session.use_strict_mode', 1);

session_start();

// Función para conectar a la base de datos
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    // Permitir caracteres UTF-8
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

// Función helper para verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Función helper para obtener el usuario actual
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $conn = getConnection();
    $user_id = $_SESSION['user_id'];
    
    // VULNERABILIDAD: SQL Injection potencial si no se sanitiza
    // Aunque aquí usamos sesión, demuestra el patrón
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    $stmt->close();
    $conn->close();
    
    return $user;
}

// Función helper para verificar acceso
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_PATH . "auth/login.php");
        exit;
    }
}

// Función helper para redireccionar si ya está logueado
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header("Location: " . BASE_PATH . "index.php");
        exit;
    }
}

// =====================================================
// VULNERABILIDAD: No hay función de sanitización global
// =====================================================
// En producción, crear funciones como:
// function sanitize($input) {
//     return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
// }

// Función para obtener el rol del usuario (para futuro uso)
function getUserRole($user_id) {
    return 'usuario'; // Simplificado para el laboratorio
}

// Función helper para generar hash MD5 del usuario
function getUserHash($user_id) {
    return md5($user_id);
}

// Función helper para obtener ID desde hash MD5
function getUserIdFromHash($hash) {
    $conn = getConnection();
    $sql = "SELECT id FROM usuarios WHERE user_hash = '$hash'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $conn->close();
        return $row['id'];
    }
    
    $conn->close();
    return null;
}
