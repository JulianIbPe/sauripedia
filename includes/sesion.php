<?php
// ============================================================
// sesion.php
// Todo lo relacionado con la sesión del visitante:
//   - inicio de sesión PHP con cookies seguras
//   - usuario conectado
//   - token CSRF (protege formularios contra envíos desde otros sitios)
//   - mensajes "flash" (se muestran una vez tras una redirección)
// Principio: Responsabilidad Única. Ningún otro archivo toca $_SESSION
// directamente para estas tareas; usan estas funciones.
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $https,  // solo viaja por HTTPS cuando el sitio lo usa
        'httponly' => true,    // JavaScript no puede leer la cookie
        'samesite' => 'Lax',   // no se envía en POST que vienen de otros sitios
    ]);
    ini_set('session.use_strict_mode', '1'); // rechaza IDs de sesión inventados
    session_start();
}

/** Nombre del usuario conectado, o null si no hay sesión. */
function usuario_actual(): ?string
{
    return $_SESSION['usuario'] ?? null;
}

/** Marca al usuario como conectado. Regenera el ID para evitar "session fixation". */
function iniciar_sesion_usuario(string $nombre): void
{
    session_regenerate_id(true);
    $_SESSION['usuario'] = $nombre;
}

/** Cierra la sesión por completo (datos + cookie). */
function cerrar_sesion_usuario(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

// ---------- Token CSRF ----------

/** Devuelve el token CSRF de la sesión (lo crea la primera vez). */
function token_csrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Campo oculto listo para pegar dentro de un <form method="POST">. */
function campo_csrf(): string
{
    return '<input type="hidden" name="csrf_token" value="' . token_csrf() . '">';
}

/** Compara en tiempo constante (hash_equals) para no filtrar información por tiempos. */
function csrf_valido(?string $token): bool
{
    return !empty($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}

// ---------- Mensajes flash ----------
// Reemplazan a los alert() de JavaScript que imprimía el PHP:
// el backend guarda el mensaje, redirige, y la página lo muestra en HTML.

function guardar_flash(string $tipo, string $mensaje, array $valores = []): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje, 'valores' => $valores];
}

/** Devuelve el mensaje pendiente y lo borra (solo se muestra una vez). */
function tomar_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}
