<?php

    session_start();
    $_SESSION = [];
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
    // (antes apuntaba a ../pages/index.html, pero index.html está en la raíz)
    header("location: ../index.html");
    exit();

?>