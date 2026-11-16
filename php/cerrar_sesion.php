<?php
// ============================================================
// cerrar_sesion.php
// Cierra la sesión. Solo acepta POST con token CSRF válido:
// así otro sitio no puede desconectar al usuario con un simple enlace o <img>.
// ============================================================
require_once __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valido($_POST['csrf_token'] ?? null)) {
    cerrar_sesion_usuario();
}

// Vuelve a la página desde la que se cerró sesión, si es del mismo sitio; si no, al inicio
$volver = $_SERVER['HTTP_REFERER'] ?? '';
$mismo_sitio = $volver !== '' && parse_url($volver, PHP_URL_HOST) === ($_SERVER['HTTP_HOST'] ?? '');
if ($mismo_sitio) {
    header('Location: ' . $volver);
    exit();
}
redirigir('index.php');
