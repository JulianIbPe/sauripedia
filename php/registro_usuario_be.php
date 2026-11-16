<?php
// ============================================================
// registro_usuario_be.php
// Procesa el formulario de pages/registro.php.
// Las validaciones del HTML (required, minlength...) solo ayudan al usuario;
// la seguridad real está acá, en el servidor.
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirigir('pages/registro.php');
}

$usuario  = trim((string) ($_POST['usuario'] ?? ''));
$correo   = trim((string) ($_POST['correo'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

/** Vuelve al formulario con un error, conservando lo escrito (menos la contraseña). */
function volver_a_registro(string $mensaje): void
{
    global $usuario, $correo;
    guardar_flash('error', $mensaje, ['usuario' => $usuario, 'correo' => $correo]);
    redirigir('pages/registro.php');
}

// ---- Validaciones ----
if (!csrf_valido($_POST['csrf_token'] ?? null)) {
    volver_a_registro('La sesión del formulario expiró. Intenta nuevamente.');
}
if ($usuario === '' || $correo === '' || $password === '') {
    volver_a_registro('Completa todos los campos.');
}
if (mb_strlen($usuario) < 3 || mb_strlen($usuario) > 50) {
    volver_a_registro('El nombre de usuario debe tener entre 3 y 50 caracteres.');
}
if (mb_strlen($correo) > 50 || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    volver_a_registro('Ingresa un correo electrónico válido (máximo 50 caracteres).');
}
if (strlen($password) < 8) {
    volver_a_registro('La contraseña debe tener al menos 8 caracteres.');
}
if (strlen($password) > 72) {
    volver_a_registro('La contraseña no puede superar los 72 caracteres.'); // bcrypt solo usa 72 bytes
}

$bd = conectar_bd();
if (!$bd) {
    volver_a_registro('No se pudo conectar con el servidor. Intenta más tarde.');
}

// ---- ¿Correo o usuario ya registrados? Una sola consulta para ambos ----
$stmt = consulta($bd,
    'SELECT correo = ? AS mismo_correo FROM usuarios WHERE correo = ? OR nombre_usuario = ? LIMIT 1',
    'sss', [$correo, $correo, $usuario]);
if ($stmt) {
    $existente = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if ($existente) {
        volver_a_registro($existente['mismo_correo']
            ? 'Este correo ya está registrado. Intenta con otro.'
            : 'Este nombre de usuario ya está registrado. Intenta con otro.');
    }
}

// ---- Alta: contraseña con bcrypt (sal automática) ----
$stmt = consulta($bd,
    'INSERT INTO usuarios (nombre_usuario, correo, password) VALUES (?, ?, ?)',
    'sss', [$usuario, $correo, password_hash($password, PASSWORD_DEFAULT)]);
$codigo_error = $stmt ? mysqli_stmt_errno($stmt) : -1;
if ($stmt) mysqli_stmt_close($stmt);

if ($codigo_error === 0) {
    guardar_flash('ok', '¡Cuenta creada! Ya puedes iniciar sesión.', ['usuario' => $usuario]);
    redirigir('pages/login.php');
}

// 1062 = clave duplicada: dos registros simultáneos con el mismo dato (lo frena el índice UNIQUE)
if ($codigo_error === 1062) {
    volver_a_registro('Ese usuario o correo ya está registrado. Intenta con otro.');
}

error_log("Sauripedia: error al registrar usuario (código $codigo_error)");
volver_a_registro('Error en el registro. Intenta nuevamente.');
