<?php
/**
 * =====================================================
 * VISUALIZAR UN TEMA
 * =====================================================
 * 
 * Muestra un tema específico con sus comentarios.
 * 
 * VULNERABILIDADES:
 * 1. SQL Injection en la consulta del tema por ID
 * 2. XSS Almacenado en los comentarios (mostrados sin sanitizar)
 * 
 * =====================================================
 */

require_once '../config/config.php';

if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$tema_id = intval($_GET['id']);
$conn = getConnection();

$tema = null;
$comentarios = [];

$tema_sql = "SELECT t.*, u.username, u.nombre_completo, u.avatar, u.id as autor_id 
             FROM temas t 
             JOIN usuarios u ON t.usuario_id = u.id 
             WHERE t.id = $tema_id";
$tema_result = $conn->query($tema_sql);

if ($tema_result && $tema_result->num_rows > 0) {
    $tema = $tema_result->fetch_assoc();
}

$comentarios_sql = "SELECT c.id, c.contenido, c.fecha_comentario, 
                    u.username, u.avatar, u.id as usuario_id 
                    FROM comentarios c 
                    JOIN usuarios u ON c.usuario_id = u.id 
                    WHERE c.tema_id = $tema_id 
                    ORDER BY c.fecha_comentario ASC";
$comentarios_result = $conn->query($comentarios_sql);

if ($comentarios_result) {
    while ($row = $comentarios_result->fetch_assoc()) {
        $comentarios[] = $row;
    }
}

$conn->close();

if (!$tema) {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tema['titulo']); ?> - Laboratorio de Seguridad</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <h1>🛡️ Laboratorio de Seguridad Web</h1>
            <nav>
                <a href="../index.php" class="btn btn-sm">← Volver al Inicio</a>
                <?php if (isLoggedIn()): ?>
                    <a href="../profile/edit.php" class="btn btn-sm">Mi Perfil</a>
                    <a href="../auth/logout.php" class="btn btn-sm btn-danger">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="../auth/login.php" class="btn btn-sm">Iniciar Sesión</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <article class="topic-detail">
            <h2><?php echo htmlspecialchars($tema['titulo']); ?></h2>
            
            <div class="topic-author">
                <?php if (!empty($tema['avatar'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($tema['avatar']); ?>" alt="Avatar" class="avatar-small">
                <?php else: ?>
                    <span class="avatar-placeholder">👤</span>
                <?php endif; ?>
                <span>
                    <a href="../profile/view.php?id=<?php echo md5($tema['autor_id']); ?>">
                        <?php echo htmlspecialchars($tema['username']); ?>
                    </a>
                </span>
                <span class="topic-date">
                    📅 <?php echo date('d/m/Y H:i', strtotime($tema['fecha_creacion'])); ?>
                </span>
            </div>
            
            <div class="topic-content">
                <?php echo nl2br(htmlspecialchars($tema['contenido'])); ?>
            </div>
        </article>
        
        <section class="comments-section">
            <h3>💬 Comentarios (<?php echo count($comentarios); ?>)</h3>
            
            <?php if (isLoggedIn()): ?>
                <form method="POST" action="../comments/add.php" class="comment-form">
                    <input type="hidden" name="tema_id" value="<?php echo $tema_id; ?>">
                    <textarea name="contenido" placeholder="Escribe tu comentario..." required></textarea>
                    <button type="submit" class="btn btn-primary">Comentar</button>
                </form>
            <?php else: ?>
                <p class="login-prompt">
                    <a href="../auth/login.php">Inicia sesión</a> para comentar.
                </p>
            <?php endif; ?>
            
            <div class="comments-list">
                <?php if (count($comentarios) > 0): ?>
                    <?php foreach ($comentarios as $com): ?>
                        <div class="comment">
                            <div class="comment-header">
                                <?php if (!empty($com['avatar'])): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($com['avatar']); ?>" alt="Avatar" class="avatar-small">
                                <?php else: ?>
                                    <span class="avatar-placeholder">👤</span>
                                <?php endif; ?>
                                <strong>
                                    <a href="../profile/view.php?id=<?php echo md5($com['usuario_id']); ?>">
                                        <?php echo htmlspecialchars($com['username']); ?>
                                    </a>
                                </strong>
                                <span class="comment-date">
                                    📅 <?php echo date('d/m/Y H:i', strtotime($com['fecha_comentario'])); ?>
                                </span>
                            </div>
                            <div class="comment-content">
                                <?php echo $com['contenido']; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-comments">No hay comentarios aún. ¡Sé el primero en comentar!</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
    
    <footer class="footer">
        <p>Laboratorio de Seguridad Web - Con fines educativos &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
