<?php
    // Inicia la sesión con cookies más seguras.
    // Se incluye en lugar de llamar directamente a session_start().
    if(session_status() === PHP_SESSION_NONE){
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => $https,   // solo por HTTPS cuando el sitio lo usa
            'httponly' => true,     // JavaScript no puede leer la cookie
            'samesite' => 'Lax'     // no se envía en peticiones POST desde otros sitios
        ]);
        ini_set('session.use_strict_mode', '1');
        session_start();
    }
?>
