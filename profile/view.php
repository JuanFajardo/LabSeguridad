<?php
/**
 * =====================================================
 * VISUALIZAR PERFIL DE USUARIO
 * =====================================================
 * 
 * Muestra el perfil de un usuario específico.
 * 
 * VULNERABILIDAD: IDOR (Insecure Direct Object Reference)
 * El ID del usuario está codificado en MD5 en la URL.
 * 
 * =====================================================
 */

require_once '../config/config.php';

if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$user_hash = $_GET['id'];
$conn = getConnection();

$user = null;
$stats = ['num_temas' => 0, 'num_comentarios' => 0];

$sql = "SELECT * FROM usuarios WHERE user_hash = '$user_hash'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    $user_id = $user['id'];
    
    $stats_sql = "SELECT 
                  (SELECT COUNT(*) FROM temas WHERE usuario_id = $user_id) as num_temas,
                  (SELECT COUNT(*) FROM comentarios WHERE usuario_id = $user_id) as num_comentarios";
    $stats_result = $conn->query($stats_sql);
    if ($stats_result) {
        $stats = $stats_result->fetch_assoc();
    }
}

$conn->close();

if (!$user) {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['username']); ?> - Perfil</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <h1>🛡️ Laboratorio de Seguridad Web</h1>
            <nav>
                <a href="../index.php" class="btn btn-sm">← Volver al Inicio</a>
                <?php if (isLoggedIn()): ?>
                    <?php if ($_SESSION['user_id'] == $user['id']): ?>
                        <a href="edit.php" class="btn btn-sm">✏️ Editar Mi Perfil</a>
                    <?php endif; ?>
                    <a href="../auth/logout.php" class="btn btn-sm btn-danger">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="../auth/login.php" class="btn btn-sm">Iniciar Sesión</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <div class="profile-view">
            <div class="profile-header">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($user['avatar']); ?>" 
                         alt="Avatar de <?php echo htmlspecialchars($user['username']); ?>" 
                         class="avatar-xlarge">
                <?php else: ?>
                    <div class="avatar-placeholder avatar-xlarge">👤</div>
                <?php endif; ?>
                
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                    <p class="full-name">
                        <?php echo htmlspecialchars($user['nombre_completo'] ?? 'Sin nombre'); ?>
                    </p>
                </div>
            </div>
            
            <div class="profile-details">
                <div class="detail-card">
                    <h4>📧 Email</h4>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                
                <div class="detail-card">
                    <h4>📝 Biografía</h4>
                    <p><?php echo nl2br(htmlspecialchars($user['bio'] ?? 'Sin biografía')); ?></p>
                </div>
                
                <div class="detail-card">
                    <h4>📅 Fecha de Registro</h4>
                    <p><?php echo date('d/m/Y', strtotime($user['fecha_registro'])); ?></p>
                </div>
            </div>
            
            <div class="profile-stats">
                <div class="stat-box">
                    <span class="stat-number"><?php echo intval($stats['num_temas']); ?></span>
                    <span class="stat-label">Temas</span>
                </div>
                <div class="stat-box">
                    <span class="stat-number"><?php echo intval($stats['num_comentarios']); ?></span>
                    <span class="stat-label">Comentarios</span>
                </div>
            </div>
            
            <?php if (isLoggedIn() && $_SESSION['user_id'] == $user['id']): ?>
                <div class="security-warning">
                    <h4>⚠️ Nota de Seguridad (Educativa)</h4>
                    <p>Este es TU perfil. El ID en la URL está codificado en MD5.</p>
                    <p>Prueba: Cambia el MD5 en la URL para ver si puedes acceder a otros perfiles...</p>
                    <p><strong>Esto demuestra la vulnerabilidad IDOR.</strong></p>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <footer class="footer">
        <p>Laboratorio de Seguridad Web - Con fines educativos &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
