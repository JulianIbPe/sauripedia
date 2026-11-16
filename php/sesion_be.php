<?php
    // Devuelve en JSON si hay una sesión iniciada y con qué nombre de usuario.
    // Lo consulta usuario.js en cada página para decidir si muestra los botones
    // "Iniciar sesión" / "Registrarse" o la foto de perfil.
    include 'config_sesion.php';

    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: no-store');

    if(isset($_SESSION['usuario'])){
        echo json_encode(['logueado' => true, 'usuario' => $_SESSION['usuario']], JSON_UNESCAPED_UNICODE);
    }
    else{
        echo json_encode(['logueado' => false]);
    }
?>
