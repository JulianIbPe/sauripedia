-- ============================================================
-- Migración para quienes YA tienen la base `login_register_db` creada.
-- Ejecutar una sola vez en phpMyAdmin (pestaña SQL, con la base seleccionada).
-- Si instalan desde cero, alcanza con login_register_db.sql.
-- ============================================================

-- Nueva tabla: intentos de login fallidos por IP (límite contra fuerza bruta).
CREATE TABLE IF NOT EXISTS `intentos_login` (
  `id`    int(11)     NOT NULL AUTO_INCREMENT,
  `ip`    varchar(45) NOT NULL,
  `fecha` timestamp   NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_intentos_ip_fecha` (`ip`, `fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Si todavía no corrieron la migración anterior (índices UNIQUE y tabla comentarios),
-- descomenten y ejecuten también esto:
-- ALTER TABLE `usuarios` ADD UNIQUE KEY `uq_nombre_usuario` (`nombre_usuario`), ADD UNIQUE KEY `uq_correo` (`correo`);
-- CREATE TABLE IF NOT EXISTS `comentarios` ( ... ver login_register_db.sql ... );
