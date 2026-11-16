<?php
// ============================================================
// login_usuario_be.php
// Procesa el formulario de pages/login.php.
// Flujo: validar método y CSRF -> frenar fuerza bruta -> verificar credenciales
//        -> iniciar sesión o volver al formulario con un mensaje.
// Patrón POST/Redirect/GET: siempre termina en una redirección, así
// recargar la página nunca reenvía la contraseña.
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/conexion.php';

// Límite de intentos fallidos POR IP, guardado en la base de datos.
// (Antes se guardaba en la sesión: bastaba con borrar la cookie para saltarlo.)
const MAX_INTENTOS   = 5;
const VENTANA_MINUTOS = 5;

/** Vuelve al formulario mostrando un error y conservando el usuario escrito. */
function volver_a_login(string $mensaje, string $usuario = ''): void
{
    guardar_flash('error', $mensaje, ['usuario' => $usuario]);
    redirigir('pages/login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirigir('pages/login.php');
}

$usuario  = trim((string) ($_POST['usuario'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

if (!csrf_valido($_POST['csrf_token'] ?? null)) {
    volver_a_login('La sesión del formulario expiró. Intenta nuevamente.', $usuario);
}
if ($usuario === '' || $password === '') {
    volver_a_login('Completa usuario y contraseña.', $usuario);
}

$bd = conectar_bd();
if (!$bd) {
    volver_a_login('No se pudo conectar con el servidor. Intenta más tarde.', $usuario);
}

$ip = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';

// ---- 1) ¿Esta IP superó el límite de intentos fallidos? ----
$stmt = consulta($bd,
    'SELECT COUNT(*) FROM intentos_login WHERE ip = ? AND fecha > NOW() - INTERVAL ? MINUTE',
    'si', [$ip, VENTANA_MINUTOS]);
$intentos = 0;
if ($stmt) {
    mysqli_stmt_bind_result($stmt, $intentos);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}
if ($intentos >= MAX_INTENTOS) {
    volver_a_login('Demasiados intentos fallidos. Espera unos minutos e intenta de nuevo.', $usuario);
}

// ---- 2) Buscar al usuario y verificar la contraseña ----
$fila = null;
$stmt = consulta($bd, 'SELECT id, nombre_usuario, password FROM usuarios WHERE nombre_usuario = ? LIMIT 1', 's', [$usuario]);
if ($stmt) {
    $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

$valido = false;
$rehash = false;
if ($fila) {
    $guardada = $fila['password'];

    if (password_verify($password, $guardada)) {
        $valido = true;
        $rehash = password_needs_rehash($guardada, PASSWORD_DEFAULT); // el algoritmo por defecto mejoró
    } elseif (strlen($guardada) === 128 && hash_equals($guardada, hash('sha512', $password))) {
        // Compatibilidad: cuentas antiguas guardadas con SHA-512 se migran a bcrypt al entrar
        $valido = true;
        $rehash = true;
    }

    if ($valido && $rehash) {
        $stmt = consulta($bd, 'UPDATE usuarios SET password = ? WHERE id = ?', 'si', [password_hash($password, PASSWORD_DEFAULT), $fila['id']]);
        if ($stmt) mysqli_stmt_close($stmt);
    }
}

// ---- 3) Resultado ----
if ($valido) {
    // Login correcto: se olvidan los intentos fallidos de esta IP
    $stmt = consulta($bd, 'DELETE FROM intentos_login WHERE ip = ?', 's', [$ip]);
    if ($stmt) mysqli_stmt_close($stmt);

    // Se guarda el nombre tal como está en la base (no el que se escribió en el formulario)
    iniciar_sesion_usuario($fila['nombre_usuario']);
    redirigir('index.php');
}

// Login fallido: se registra el intento y se limpian los registros viejos (más de un día)
$stmt = consulta($bd, 'INSERT INTO intentos_login (ip) VALUES (?)', 's', [$ip]);
if ($stmt) mysqli_stmt_close($stmt);
$stmt = consulta($bd, 'DELETE FROM intentos_login WHERE fecha < NOW() - INTERVAL 1 DAY');
if ($stmt) mysqli_stmt_close($stmt);

// Mismo mensaje para "usuario inexistente" y "contraseña incorrecta": no revela qué usuarios existen
volver_a_login('Usuario o contraseña incorrectos. Verifica los datos ingresados.', $usuario);
