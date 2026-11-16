-- ============================================================
-- Base de datos de Sauripedia (instalación desde cero).
-- Importar en phpMyAdmin: crea la base y sus tres tablas.
-- Si ya tienen la base creada de antes, usen migracion_2026-11.sql.
-- ============================================================

CREATE DATABASE IF NOT EXISTS `login_register_db`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;
USE `login_register_db`;

-- Usuarios registrados. UNIQUE evita duplicados aunque lleguen dos registros a la vez.
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id`             int(11)      NOT NULL AUTO_INCREMENT,
  `nombre_usuario` varchar(50)  NOT NULL,
  `correo`         varchar(50)  NOT NULL,
  `password`       varchar(255) NOT NULL,  -- hash bcrypt, nunca la contraseña en texto plano
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_nombre_usuario` (`nombre_usuario`),
  UNIQUE KEY `uq_correo` (`correo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Comentarios. Si se borra un usuario, se borran sus comentarios (ON DELETE CASCADE).
CREATE TABLE IF NOT EXISTS `comentarios` (
  `id`         int(11)      NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11)      NOT NULL,
  `texto`      varchar(500) NOT NULL,
  `fecha`      timestamp    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_comentarios_usuario` (`usuario_id`),
  CONSTRAINT `fk_comentarios_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Intentos de login fallidos, por IP. Los usa php/login_usuario_be.php para
-- bloquear la fuerza bruta (5 fallos en 5 minutos). Se limpian solos después de un día.
CREATE TABLE IF NOT EXISTS `intentos_login` (
  `id`    int(11)     NOT NULL AUTO_INCREMENT,
  `ip`    varchar(45) NOT NULL,  -- 45 caracteres alcanzan para IPv6
  `fecha` timestamp   NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_intentos_ip_fecha` (`ip`, `fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
