-- =====================================================
-- ACTUALIZACIÓN: Agregar columna user_hash para IDOR con MD5
-- =====================================================
-- Ejecutar este script para agregar la columna MD5 a la tabla
-- y poblar los hash para los usuarios existentes.
-- =====================================================

USE security_lab;

-- Agregar columna user_hash si no existe
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS user_hash VARCHAR(32) NULL;

-- Generar MD5 para usuarios existentes
UPDATE usuarios SET user_hash = MD5(id) WHERE user_hash IS NULL;

-- Los nuevos usuarios obtendrán su hash al registrarse
-- (necesitas agregar esto en register.php y edit.php del perfil)
