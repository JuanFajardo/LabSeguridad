# AGENTS.md

**Última actualización:** 16 de abril de 2026

---

## Proyecto: Laboratorio de Seguridad Web

Sistema PHP/MySQL educativo para demostrar vulnerabilidades web comunes.

---

## Comandos de Build/Test

### Configuración de Base de Datos
```bash
# Importar schema MySQL
mysql -u root -p < database/schema.sql

# O vía phpMyAdmin
# Importar database/schema.sql manualmente
```

### Servidor Local (XAMPP)
```bash
# Acceder a http://localhost/security_lab/
```

### Credenciales de Prueba
| Usuario | Contraseña |
|---------|------------|
| admin | admin123 |
| juan_perez | juan123 |

---

## Convenciones de Código

### PHP
- **Estándar:** PHP 7.4+
- **Encoding:** UTF-8 (utf8mb4 en MySQL)
- **Fin de línea:** LF
- **Indentación:** 4 espacios

### Nomenclatura
| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| Variables | $snake_case | `$user_id` |
| Constantes | UPPER_SNAKE | `BASE_PATH` |
| Funciones | snake_case | `getConnection()` |
| Clases | PascalCase | `DatabaseConnection` |
| Archivos | snake_case | `config.php` |

### Comentarios Educativos
```php
/**
 * =====================================================
 * VULNERABILIDAD: [NOMBRE]
 * =====================================================
 * Descripción de la vulnerabilidad
 * Payload de prueba:
 * [ejemplo de ataque]
 * 
 * DEMOSTRACIÓN EDUCATIVA
 * =====================================================
 */
```

---

## Estructura del Proyecto

```
security_lab/
├── config/config.php       # Configuración (vulnerable)
├── database/schema.sql      # Schema MySQL
├── auth/                    # Autenticación (SQLi)
├── topics/                  # Temas (SQLi + XSS)
├── comments/               # Comentarios (XSS)
├── profile/                # Perfil (IDOR + Upload)
├── uploads/                # Archivos subidos
├── css/style.css           # Estilos
└── js/script.js            # JavaScript vulnerable
```

---

## Vulnerabilidades Intencionales

1. **SQL Injection** - `auth/login.php:40`, `topics/view.php:25`
2. **XSS Almacenado** - `topics/view.php:130`, `comments/add.php:25`
3. **File Upload** - `profile/edit.php:35`
4. **IDOR** - `profile/view.php:20`
5. **Session Hijacking** - `config/config.php:20`
6. **Clickjacking** - Sin headers X-Frame-Options

---

## Arreglos Recomendados

### SQL Injection
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
```

### XSS
```php
echo htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
```

### File Upload
```php
$allowed = ['jpg', 'jpeg', 'png', 'gif'];
$ext = pathinfo($file, PATHINFO_EXTENSION);
```

### IDOR
```php
if ($current_user->id !== $requested_id && !isAdmin()) {
    die("Acceso denegado");
}
```

---

## Notas Importantes

- Proyecto SOLO para fines educativos
- NUNCA usar en producción
- Mantener comentarios que expliquen vulnerabilidades
