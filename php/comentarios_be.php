<?php
// ============================================================
// comentarios_be.php  (API JSON que usa javascript/comentarios.js)
//   GET  -> últimos 50 comentarios (público)
//   POST -> publica un comentario (sesión iniciada + token CSRF en la cabecera X-CSRF-Token)
// Siempre responde JSON, incluso si falla la base de datos.
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/conexion.php';

const MAX_CARACTERES  = 500;
const ESPERA_SEGUNDOS = 10; // tiempo mínimo entre comentarios del mismo usuario (anti-spam)

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');

/** Envía la respuesta JSON con su código HTTP y termina. */
function responder(int $codigo, array $datos): void
{
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit();
}

$bd = conectar_bd();
if (!$bd) {
    responder(503, ['ok' => false, 'error' => 'No se pudo conectar con el servidor. Intenta más tarde.']);
}

// ---------- Listar ----------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = consulta($bd,
        'SELECT c.id, u.nombre_usuario AS usuario, c.texto, c.fecha
           FROM comentarios c
           JOIN usuarios u ON u.id = c.usuario_id
          ORDER BY c.id DESC
          LIMIT 50');
    $lista = $stmt ? mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC) : [];
    responder(200, ['ok' => true, 'comentarios' => $lista]);
}

// ---------- Publicar ----------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, ['ok' => false, 'error' => 'Método no permitido.']);
}
if (usuario_actual() === null) {
    responder(401, ['ok' => false, 'error' => 'Debes iniciar sesión para comentar.']);
}
if (!csrf_valido($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
    responder(403, ['ok' => false, 'error' => 'Petición no válida. Recarga la página e intenta de nuevo.']);
}
if (!empty($_SESSION['ultimo_comentario']) && time() - $_SESSION['ultimo_comentario'] < ESPERA_SEGUNDOS) {
    responder(429, ['ok' => false, 'error' => 'Espera unos segundos antes de publicar otro comentario.']);
}

$datos = json_decode(file_get_contents('php://input'), true);
$texto = is_array($datos) ? trim((string) ($datos['comentario'] ?? '')) : '';
// Normaliza saltos de línea y quita caracteres de control invisibles
$texto = preg_replace('/[^\P{C}\n]+/u', '', str_replace("\r\n", "\n", $texto));

if ($texto === '') {
    responder(400, ['ok' => false, 'error' => 'El comentario no puede estar vacío.']);
}
if (mb_strlen($texto) > MAX_CARACTERES) {
    responder(400, ['ok' => false, 'error' => 'El comentario no puede superar los ' . MAX_CARACTERES . ' caracteres.']);
}

// El autor sale de la sesión, nunca de lo que envíe el navegador
$stmt = consulta($bd,
    'INSERT INTO comentarios (usuario_id, texto) SELECT id, ? FROM usuarios WHERE nombre_usuario = ?',
    'ss', [$texto, usuario_actual()]);
$guardado = $stmt && mysqli_stmt_errno($stmt) === 0 && mysqli_stmt_affected_rows($stmt) > 0;

if (!$guardado) {
    error_log('Sauripedia: no se pudo guardar el comentario');
    responder(500, ['ok' => false, 'error' => 'No se pudo guardar el comentario. Intenta nuevamente.']);
}

$_SESSION['ultimo_comentario'] = time();
responder(201, ['ok' => true]);
