<?php
/**
 * =====================================================
 * PÁGINA PRINCIPAL - LISTADO DE TEMAS
 * =====================================================
 * 
 * Muestra todos los temas del sistema ordenados por fecha.
 * =====================================================
 */

require_once 'config/config.php';

$conn = getConnection();
$sql = "SELECT t.*, u.username, u.nombre_completo 
        FROM temas t 
        JOIN usuarios u ON t.usuario_id = u.id 
        ORDER BY t.fecha_creacion DESC";
$result = $conn->query($sql);
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Laboratorio de Seguridad Web</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <h1>🛡️ Laboratorio de Seguridad Web</h1>
            <nav>
                <?php if (isLoggedIn()): ?>
                    <span class="user-info">👤 <?php echo $_SESSION['username']; ?></span>
                    <a href="topics/create.php" class="btn btn-sm">+ Nuevo Tema</a>
                    <a href="profile/edit.php" class="btn btn-sm">Mi Perfil</a>
                    <a href="auth/logout.php" class="btn btn-sm btn-danger">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="auth/login.php" class="btn btn-sm">Iniciar Sesión</a>
                    <a href="auth/register.php" class="btn btn-sm">Registrarse</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <section class="topics-section">
            <div class="section-header">
                <h2>📋 Temas del Foro</h2>
                <?php if (isLoggedIn()): ?>
                    <a href="topics/create.php" class="btn btn-primary">Crear Nuevo Tema</a>
                <?php endif; ?>
            </div>
            
            <?php if ($result && $result->num_rows > 0): ?>
                <div class="topics-list">
                    <?php while ($tema = $result->fetch_assoc()): ?>
                        <article class="topic-card">
                            <h3>
                                <a href="topics/view.php?id=<?php echo $tema['id']; ?>">
                                    <?php echo htmlspecialchars($tema['titulo']); ?>
                                </a>
                            </h3>
                            <p class="topic-preview"><?php echo substr(htmlspecialchars($tema['contenido']), 0, 200); ?>...</p>
                            <div class="topic-meta">
                                <span>👤 <?php echo htmlspecialchars($tema['username']); ?></span>
                                <span>📅 <?php echo date('d/m/Y H:i', strtotime($tema['fecha_creacion'])); ?></span>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="no-topics">No hay temas disponibles. ¡Sé el primero en crear uno!</p>
            <?php endif; ?>
        </section>
        
        <aside class="sidebar">
            <div class="info-box">
                <h3>📚 Sobre el Laboratorio</h3>
                <p>Este sistema está diseñado para aprender sobre vulnerabilidades web de manera segura.</p>
                <h4>Vulnerabilidades Presentes:</h4>
                <ul>
                    <li>SQL Injection</li>
                    <li>XSS Almacenado</li>
                    <li>Subida insegura de archivos</li>
                    <li>IDOR</li>
                    <li>Sesiones inseguras</li>
                </ul>
            </div>
        </aside>
    </main>
    
    <footer class="footer">
        <p>Laboratorio de Seguridad Web - wendoline@pagina.edu.bo &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
