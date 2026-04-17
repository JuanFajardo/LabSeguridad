<?php
/**
 * =====================================================
 * EDITAR PERFIL DE USUARIO
 * =====================================================
 * 
 * Permite al usuario modificar su información personal
 * y subir una imagen de avatar.
 * 
 * VULNERABILIDADES:
 * 1. SQL Injection en UPDATE
 * 2. File Upload sin validación adecuada
 * 3. Permite subir archivos PHP maliciosos
 * 
 * =====================================================
 */

require_once '../config/config.php';
requireLogin();

$user = getCurrentUser();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_completo = trim($_POST['nombre_completo'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $conn = getConnection();
    
    /**
     * =====================================================
     * VULNERABILIDAD: File Upload Inseguro
     * =====================================================
     * No se valida:
     * - Tipo MIME real del archivo
     * - Extensión del archivo
     * - Contenido del archivo
     * - Tamaño máximo
     * 
     * Un atacante podría subir archivos PHP maliciosos
     * y ejecutarlos accediendo directamente.
     * 
     * PAYLOAD: Subir shell.php con contenido:
     * <?php system($_GET['cmd']); ?>
     * 
     * DEMOSTRACIÓN EDUCATIVA
     * =====================================================
     */
    
    $avatar_filename = $user['avatar']; // Mantener avatar actual si no se sube nuevo
    
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../uploads/';
        
        // Crear directorio si no existe
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $tmp_name = $_FILES['avatar']['name'];
        $new_filename = time() . '_' . basename($tmp_name);
        $target_path = $upload_dir . $new_filename;
        
        /**
         * VULNERABILIDAD: Sin validación de tipo de archivo
         * Solo mueve el archivo sin verificar seguridad
         */
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_path)) {
            $avatar_filename = $new_filename;
        } else {
            $error = "Error al subir el archivo.";
        }
    }
    
    if (empty($error)) {
        /**
         * =====================================================
         * VULNERABILIDAD: SQL INJECTION
         * =====================================================
         * Los datos se actualizan sin prepared statements.
         * =====================================================
         */
        $sql = "UPDATE usuarios SET 
                nombre_completo = '$nombre_completo', 
                bio = '$bio', 
                avatar = " . ($avatar_filename ? "'$avatar_filename'" : "NULL") . " 
                WHERE id = " . $_SESSION['user_id'];
        
        if ($conn->query($sql) === TRUE) {
            // Actualizar datos en sesión
            $_SESSION['nombre_completo'] = $nombre_completo;
            $success = "¡Perfil actualizado exitosamente!";
            $user = getCurrentUser(); // Recargar datos
        } else {
            $error = "Error al actualizar: " . $conn->error;
        }
    }
    
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Laboratorio de Seguridad</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <h1>🛡️ Laboratorio de Seguridad Web</h1>
            <nav>
                <a href="../index.php" class="btn btn-sm">← Volver al Inicio</a>
                <a href="../auth/logout.php" class="btn btn-sm btn-danger">Cerrar Sesión</a>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <div class="profile-edit">
            <h2>⚙️ Editar Mi Perfil</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="profile-preview">
                <?php if ($user['avatar']): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($user['avatar']); ?>" 
                         alt="Avatar actual" class="avatar-large">
                <?php else: ?>
                    <div class="avatar-placeholder avatar-large">👤</div>
                <?php endif; ?>
                <p><strong>Usuario:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nombre_completo">Nombre Completo:</label>
                    <input type="text" id="nombre_completo" name="nombre_completo"
                           value="<?php echo htmlspecialchars($user['nombre_completo'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="bio">Biografía:</label>
                    <textarea id="bio" name="bio" rows="4"
                              placeholder="Cuéntanos sobre ti..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="avatar">Avatar (imagen de perfil):</label>
                    <input type="file" id="avatar" name="avatar" accept="image/*">
                    <small class="form-help">
                        ⚠️ Nota: Este formulario permite subir cualquier tipo de archivo.
                        En un sistema real, validar el tipo de archivo es CRUCIAL.
                    </small>
                </div>
                
                <div class="form-actions">
                    <a href="../index.php" class="btn">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </main>
    
    <footer class="footer">
        <p>Laboratorio de Seguridad Web - Con fines educativos &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
