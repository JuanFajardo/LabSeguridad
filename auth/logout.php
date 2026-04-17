<?php
/**
 * =====================================================
 * CIERRE DE SESIÓN
 * =====================================================
 * 
 * Destruye la sesión actual y redirige al login.
 * 
 * NOTA: No hay protección contra Session Fixation
 * porque la sesión se regenera solo si está configurado.
 * =====================================================
 */

require_once '../config/config.php';

// Destruir todas las variables de sesión
$_SESSION = array();

// Destruir la cookie de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Redirigir al login
header("Location: login.php");
exit;
