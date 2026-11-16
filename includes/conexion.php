<?php
// ============================================================
// conexion.php
// Abre la conexión a MySQL/MariaDB y la devuelve.
// A diferencia del conexion_be.php anterior, NO imprime nada:
// si falla devuelve null y cada script decide cómo avisar
// (JSON para comentarios, mensaje en la página para login/registro).
// Principio: separar el acceso a datos de la presentación.
// ============================================================

/**
 * Credenciales: se leen de variables de entorno (DB_HOST, DB_USER, DB_PASS, DB_NAME)
 * para no dejar contraseñas fijas al subir a un servidor real.
 * Si no existen, usa la configuración por defecto de XAMPP.
 */
function conectar_bd(): ?mysqli
{
    // Patrón Singleton simple: una sola conexión por petición aunque se llame varias veces
    static $conexion = null;
    if ($conexion !== null) {
        return $conexion;
    }

    mysqli_report(MYSQLI_REPORT_OFF); // manejamos los errores nosotros

    $conexion = @mysqli_connect(
        getenv('DB_HOST') ?: 'localhost',
        getenv('DB_USER') ?: 'root',
        getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
        getenv('DB_NAME') ?: 'login_register_db'
    );

    if (!$conexion) {
        // El detalle va al log del servidor, nunca al visitante
        error_log('Sauripedia: error de conexión a la base de datos: ' . mysqli_connect_error());
        $conexion = null;
        return null;
    }

    // utf8mb4 coincide con las tablas y evita problemas con tildes, ñ y emojis
    mysqli_set_charset($conexion, 'utf8mb4');
    return $conexion;
}

/**
 * Ejecuta una consulta preparada y devuelve el statement ya ejecutado (o null si falló).
 * Evita repetir prepare + bind + execute en cada script (DRY) y siempre usa
 * parámetros enlazados: nunca se concatena texto del usuario en el SQL (sin inyección SQL).
 *
 * Ej.: consulta($bd, "SELECT id FROM usuarios WHERE correo = ?", "s", [$correo])
 */
function consulta(mysqli $bd, string $sql, string $tipos = '', array $parametros = []): ?mysqli_stmt
{
    $stmt = mysqli_prepare($bd, $sql);
    if (!$stmt) {
        error_log('Sauripedia: error al preparar consulta: ' . mysqli_error($bd));
        return null;
    }
    if ($tipos !== '') {
        mysqli_stmt_bind_param($stmt, $tipos, ...$parametros);
    }
    if (!mysqli_stmt_execute($stmt)) {
        error_log('Sauripedia: error al ejecutar consulta: ' . mysqli_stmt_error($stmt));
        // Se devuelve igual para que quien llama pueda leer el código de error (ej. 1062)
    }
    return $stmt;
}
