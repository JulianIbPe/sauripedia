-- Ejecutar UNA vez en phpMyAdmin sobre la base `login_register_db` si la tabla ya existe.
-- (Si importás login_register_db.sql desde cero, no hace falta.)

-- 1) Si la columna se creó con el nombre mal escrito "coreo", corregirla:
--    (omitir esta línea si ya se llama `correo`)
-- ALTER TABLE `usuarios` CHANGE `coreo` `correo` VARCHAR(50) NOT NULL;

-- 2) Evita duplicados a nivel de base de datos (aunque falle el PHP o se envíe dos veces):
ALTER TABLE `usuarios`
  ADD UNIQUE KEY `uq_nombre_usuario` (`nombre_usuario`),
  ADD UNIQUE KEY `uq_correo` (`correo`);

-- Si el paso 2 da error "Duplicate entry", hay filas repetidas: borrarlas con
-- DELETE u1 FROM usuarios u1 JOIN usuarios u2
--   ON u1.correo = u2.correo AND u1.id > u2.id;

-- 3) Tabla de comentarios (nueva):

--
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `texto` varchar(500) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_comentarios_usuario` (`usuario_id`),
  CONSTRAINT `fk_comentarios_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
