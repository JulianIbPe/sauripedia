<?php

    include 'config_sesion.php';

    $_SESSION = [];
    // Borra también la cookie de sesión del navegador
    if(ini_get('session.use_cookies')){
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();

    // Si lo llama usuario.js (fetch con POST) respondemos JSON y la página
    // se actualiza sola, sin recargar.
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-store');
        echo json_encode(['ok' => true]);
        exit();
    }

    // Si se abre directamente en el navegador, vuelve al inicio.
    header("location: ../index.html");
    exit();

?>
