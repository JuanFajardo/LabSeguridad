-- =====================================================
-- LABORATORIO DE SEGURIDAD WEB - SCHEMA DE BASE DE DATOS
-- =====================================================
-- Este archivo crea las tablas necesarias para el sistema.
-- Úsalo para configurar la base de datos MySQL.
--
-- VULNERABILIDAD EDUCATIVA: Las contraseñas se almacenan
-- en texto plano (no hash) para demostrar riesgos.
-- NUNCA hacer esto en producción.
-- =====================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS ucb;
USE ucb;

-- Tabla de usuarios
-- NOTA: Esta tabla NO usa password_hash() por razones educativas
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(50) NOT NULL,
    nombre_completo VARCHAR(100),
    bio TEXT,
    avatar VARCHAR(255) DEFAULT NULL,
    user_hash VARCHAR(32) DEFAULT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_user_hash (user_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de temas (topics)
CREATE TABLE IF NOT EXISTS temas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    contenido TEXT NOT NULL,
    usuario_id INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de comentarios
CREATE TABLE IF NOT EXISTS comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tema_id INT NOT NULL,
    usuario_id INT NOT NULL,
    contenido TEXT NOT NULL,
    fecha_comentario TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tema_id) REFERENCES temas(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_tema (tema_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar datos de prueba CON sus hashes MD5
INSERT INTO usuarios (username, email, password, nombre_completo, bio, user_hash) VALUES
('admin', 'admin@seguridad.local', 'admin123', 'Administrador del Sistema', 'Superusuario del laboratorio', MD5(1)),
('juan_perez', 'juan@ejemplo.com', 'juan123', 'Juan Pérez', 'Estudiante de seguridad informatica', MD5(2)),
('maria_garcia', 'maria@ejemplo.com', 'maria123', 'María García', 'Analista de sistemas', MD5(3)),
('carlos_rodriguez', 'carlos@ejemplo.com', 'carlos123', 'Carlos Rodríguez', 'Pentester en formación', MD5(4));

INSERT INTO temas (titulo, contenido, usuario_id) VALUES
('Bienvenidos al Laboratorio de Seguridad', 'Este es un espacio para aprender sobre vulnerabilidades web de manera segura y controlada.', 1),
('¿Qué es el SQL Injection?', 'El SQL Injection es una técnica que aprovecha errores en la validación de entrada de datos para ejecutar consultas SQL no autorizadas.', 1),
('Introducción al XSS', 'Cross-Site Scripting permite injectar scripts maliciosos en páginas web visitadas por otros usuarios.', 2);

INSERT INTO comentarios (tema_id, usuario_id, contenido) VALUES
(1, 2, '¡Excelente iniciativa! Muy útil para aprender.'),
(1, 3, 'Espero aprender mucho con este laboratorio.'),
(2, 4, '¿Alguien tiene ejemplos prácticos de SQL Injection?'),
(3, 1, 'El XSS almacenado es particularmente peligroso porque persiste en la página.');
