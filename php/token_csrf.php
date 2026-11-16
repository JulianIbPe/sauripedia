<?php
    // Entrega el token CSRF de la sesión actual en JSON.
    // El navegador lo envía de vuelta en la cabecera X-CSRF-Token al publicar,
    // y el servidor comprueba que coincida (ver comentarios_be.php).
    include 'config_sesion.php';

    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: no-store');

    if(empty($_SESSION['csrf_token'])){
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    echo json_encode(['token' => $_SESSION['csrf_token']]);
?>
