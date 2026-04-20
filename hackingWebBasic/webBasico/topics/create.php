<?php
/**
 * =====================================================
 * CREAR NUEVO TEMA
 * =====================================================
 * 
 * Formulario para crear un nuevo tema en el foro.
 * Requiere estar autenticado.
 * 
 * VULNERABILIDAD: SQL Injection en el INSERT
 * =====================================================
 */

require_once '../config/config.php';
requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');
    
    if (empty($titulo) || empty($contenido)) {
        $error = "Por favor, complete todos los campos.";
    } else {
        $conn = getConnection();
        $usuario_id = $_SESSION['user_id'];
        
        /**
         * =====================================================
         * VULNERABILIDAD: SQL INJECTION
         * =====================================================
         * El contenido se inserta directamente sin sanitizar.
         * Se podría insertar código SQL malicioso.
         * =====================================================
         */
        $sql = "INSERT INTO temas (titulo, contenido, usuario_id) 
                VALUES ('$titulo', '$contenido', $usuario_id)";
        
        if ($conn->query($sql) === TRUE) {
            $success = "¡Tema creado exitosamente!";
            header("Refresh: 1; URL=../index.php");
        } else {
            $error = "Error al crear el tema: " . $conn->error;
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
    <title>Crear Tema - Laboratorio de Seguridad</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <h1>🛡️ Laboratorio de Seguridad Web</h1>
            <nav>
                <a href="../index.php" class="btn btn-sm">← Volver al Inicio</a>
                <a href="../profile/edit.php" class="btn btn-sm">Mi Perfil</a>
                <a href="../auth/logout.php" class="btn btn-sm btn-danger">Cerrar Sesión</a>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <div class="form-box">
            <h2>📝 Crear Nuevo Tema</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="titulo">Título del Tema: *</label>
                        <input type="text" id="titulo" name="titulo" required
                               placeholder="Escribe un título descriptivo"
                               value="<?php echo $_POST['titulo'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="contenido">Contenido: *</label>
                        <textarea id="contenido" name="contenido" rows="10" required
                                  placeholder="Escribe el contenido de tu tema..."><?php echo $_POST['contenido'] ?? ''; ?></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <a href="../index.php" class="btn">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Publicar Tema</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
