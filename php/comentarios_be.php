<?php
    // GET  -> devuelve los últimos comentarios en JSON (público).
    // POST -> publica un comentario (solo con sesión iniciada y token CSRF válido).
    include 'config_sesion.php';
    include 'conexion_be.php';

    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: no-store');

    function responder($codigo, $datos){
        http_response_code($codigo);
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        exit();
    }

    $MAX_CARACTERES = 500;
    $ESPERA_SEGUNDOS = 10;

    // ---------- Listar ----------
    if($_SERVER['REQUEST_METHOD'] === 'GET'){
        $consulta = "SELECT c.id, u.nombre_usuario AS usuario, c.texto, c.fecha
                     FROM comentarios c
                     JOIN usuarios u ON u.id = c.usuario_id
                     ORDER BY c.id DESC
                     LIMIT 50";
        $resultado = mysqli_query($conexion, $consulta);
        $lista = [];
        if($resultado){
            while($fila = mysqli_fetch_assoc($resultado)){
                $lista[] = $fila;
            }
        }
        mysqli_close($conexion);
        responder(200, ['comentarios' => $lista]);
    }

    // ---------- Publicar ----------
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        responder(405, ['ok' => false, 'error' => 'Método no permitido.']);
    }

    if(empty($_SESSION['usuario'])){
        responder(401, ['ok' => false, 'error' => 'Debes iniciar sesión para comentar.']);
    }

    // Token CSRF: debe venir en la cabecera y coincidir con el de la sesión
    $token_enviado = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if(empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token_enviado)){
        responder(403, ['ok' => false, 'error' => 'Petición no válida. Recarga la página e intenta de nuevo.']);
    }

    // Espera mínima entre comentarios (anti-spam)
    if(!empty($_SESSION['ultimo_comentario']) && (time() - $_SESSION['ultimo_comentario']) < $ESPERA_SEGUNDOS){
        responder(429, ['ok' => false, 'error' => 'Espera unos segundos antes de publicar otro comentario.']);
    }

    $datos = json_decode(file_get_contents('php://input'), true);
    $texto = is_array($datos) ? trim((string)($datos['comentario'] ?? '')) : '';
    // Normaliza saltos de línea y quita caracteres de control
    $texto = preg_replace('/[^\P{C}\n]+/u', '', str_replace("\r\n", "\n", $texto));

    if($texto === ''){
        responder(400, ['ok' => false, 'error' => 'El comentario no puede estar vacío.']);
    }
    if(mb_strlen($texto) > $MAX_CARACTERES){
        responder(400, ['ok' => false, 'error' => "El comentario no puede superar los $MAX_CARACTERES caracteres."]);
    }

    // El autor sale de la sesión (no de lo que envíe el navegador)
    $stmt = mysqli_prepare($conexion, "INSERT INTO comentarios (usuario_id, texto) SELECT id, ? FROM usuarios WHERE nombre_usuario = ?");
    mysqli_stmt_bind_param($stmt, "ss", $texto, $_SESSION['usuario']);
    $ok = mysqli_stmt_execute($stmt);
    $filas = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);

    if(!$ok || $filas < 1){
        error_log('Sauripedia: no se pudo guardar el comentario');
        responder(500, ['ok' => false, 'error' => 'No se pudo guardar el comentario. Intenta nuevamente.']);
    }

    $_SESSION['ultimo_comentario'] = time();
    responder(201, ['ok' => true]);
?>
