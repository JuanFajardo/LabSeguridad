# 🛡️ Laboratorio de Seguridad Web

Sistema web educativo para aprender sobre vulnerabilidades comunes en aplicaciones PHP/MySQL.

**⚠️ ADVERTENCIA:** Este proyecto CONTIENE INTENCIONALMENTE vulnerabilidades de seguridad. NUNCA lo use en producción.

---

## 📋 Requisitos

- PHP 7.4+ (o 8.x)
- MySQL / MariaDB
- Servidor web (Apache con XAMPP, WAMP, etc.)
- Navegador web moderno

---

## 🚀 Instalación

### 1. Configurar la base de datos

1. Abra phpMyAdmin (http://localhost/phpmyadmin)
2. Importe el archivo `database/schema.sql`:
   ```bash
   # O usando MySQL CLI:
   mysql -u root -p < database/schema.sql
   ```

### 2. Configurar el proyecto

1. Copie los archivos al directorio web:
   ```
   c:/xampp/htdocs/security_lab/
   ```

2. Configure la conexión en `config/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Su contraseña si tiene
   define('DB_NAME', 'ucb_lab');
   ```

3. Cree el directorio de uploads:
   ```bash
   mkdir uploads
   chmod 777 uploads
   ```

### 3. Acceder al laboratorio

Abra en su navegador:
```
http://localhost/ucb_lab/
```

---

## 🔐 Datos de Prueba

| Usuario | Contraseña | Rol |
|---------|------------|-----|
| admin | admin123 | Administrador |
| juan_perez | juan123 | Usuario |
| maria_garcia | maria123 | Usuario |
| carlos_rodriguez | carlos123 | Usuario |

---

## 🎯 Vulnerabilidades Presentes

### 1. SQL Injection (SQLi)
**Ubicación:** `auth/login.php`, `topics/view.php`, `comments/add.php`

**Demostración:**
```sql
-- En el login, pruebe:
Usuario: ' OR '1'='1
Contraseña: ' OR '1'='1

-- En ver tema (?id=):
?id=1 UNION SELECT 1,username,password,4,5 FROM usuarios--
```

### 2. XSS Almacenado (Stored XSS)
**Ubicación:** `topics/view.php` (comentarios)

**Demostración:**
```html
<!-- En un comentario, pruebe: -->
<script>alert('XSS!');</script>

<!-- O: -->
<img src=x onerror="alert(document.cookie)">
```

### 3. File Upload Vulnerable
**Ubicación:** `profile/edit.php`

**Demostración:**
1. Suba una imagen de perfil normal
2. Intente subir un archivo `.php`:
```php
<?php
system($_GET['cmd']);
?>
```
3. Acceda a: `uploads/shell.php?cmd=whoami`

### 4. IDOR (Insecure Direct Object Reference)
**Ubicación:** `profile/view.php?id=BASE64`

**Demostración:**
1. Vaya a su perfil, observe el Base64 en la URL
2. Cambie el Base64 para ver otros perfiles
3. Sin autenticación requerida

### 5. Session Hijacking
**Ubicación:** `config/config.php`

**Demostración:**
1. Inicie sesión
2. Via XSS: `<script>alert(document.cookie)</script>`
3. Observe que la cookie NO tiene flags HttpOnly

### 6. Clickjacking
**Ubicación:** Todas las páginas (sin X-Frame-Options)

**Demostración:**
```html
<iframe src="http://localhost/security_lab/" width="100%" height="600px">
```

---

## 📁 Estructura del Proyecto

```
security_lab/
├── config/
│   └── config.php          # Configuración y conexión a BD
├── database/
│   └── schema.sql          # Estructura de la base de datos
├── auth/
│   ├── login.php           # Inicio de sesión
│   ├── register.php        # Registro de usuarios
│   └── logout.php         # Cerrar sesión
├── topics/
│   ├── index.php           # Listado de temas
│   ├── create.php          # Crear tema
│   └── view.php            # Ver tema (SQLi + XSS)
├── comments/
│   └── add.php             # Agregar comentario (XSS)
├── profile/
│   ├── edit.php            # Editar perfil (Upload vuln)
│   └── view.php           # Ver perfil (IDOR)
├── uploads/                # Archivos subidos (EJECUTABLE!)
├── css/
│   └── style.css           # Estilos
├── js/
│   └── script.js          # JavaScript
├── .htaccess              # Configuración Apache
└── README.md              # Este archivo
```

---

## 🔧 Cómo Usar Este Laboratorio

### Para Estudiantes:

1. **Explore** las vulnerabilidades usando los payloads de prueba
2. **Documente** cada vulnerabilidad encontrada
3. **Proponga** correcciones para cada problema

### Para Profesores:

1. Use este sistema como base para evaluaciones
2. Pida a los estudiantes que encuentren y exploten vulnerabilidades
3. Evalúe las soluciones propuestas por los estudiantes

---

## ✅ Cómo Corregir las Vulnerabilidades

### SQL Injection
```php
// ANTES (vulnerable):
$sql = "SELECT * FROM users WHERE username = '$username'";

// DESPUÉS (seguro):
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
```

### XSS
```php
// ANTES (vulnerable):
echo $comentario['contenido'];

// DESPUÉS (seguro):
echo htmlspecialchars($comentario['contenido'], ENT_QUOTES, 'UTF-8');
```

### File Upload
```php
// Validar tipo MIME y extensión
$allowed = ['jpg', 'jpeg', 'png', 'gif'];
$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) {
    die("Tipo de archivo no permitido");
}
```

### IDOR
```php
// Verificar permisos antes de mostrar datos
if ($current_user->id !== $requested_user->id && !$current_user->isAdmin()) {
    die("Acceso denegado");
}
```

---

## 📚 Recursos de Aprendizaje

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PortSwigger Web Security Academy](https://portswigger.net/web-security)
- [PHP Security Guide](https://www.php.net/manual/en/security.php)

---

## ⚠️ Descargo de Responsabilidad

Este proyecto es SOLO para fines educativos. Las vulnerabilidades son INTENCIONALES y NO deben replicarse en aplicaciones reales.

**El autor NO se responsabiliza por el mal uso de este código.**

---

© <?php echo date('Y'); ?> - Laboratorio de Seguridad Web Educativo
