<?php

    include 'conexion_be.php';

    // Muestra un mensaje y vuelve al formulario de registro.
    function volver_a_registro($mensaje){
        echo '
            <script>
                alert(' . json_encode($mensaje, JSON_UNESCAPED_UNICODE) . ');
                window.location = "../pages/singin.html";
            </script>
        ';
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        header("location: ../pages/singin.html");
        exit();
    }

    $usuario  = trim($_POST['usuario'] ?? '');
    $correo   = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    // ---- Validaciones del lado del servidor (el HTML solo ayuda, no protege) ----
    if($usuario === '' || $correo === '' || $password === ''){
        volver_a_registro("Completa todos los campos.");
    }
    if(mb_strlen($usuario) < 3 || mb_strlen($usuario) > 50){
        volver_a_registro("El nombre de usuario debe tener entre 3 y 50 caracteres.");
    }
    if(mb_strlen($correo) > 50 || !filter_var($correo, FILTER_VALIDATE_EMAIL)){
        volver_a_registro("Ingresa un correo electrónico válido (máximo 50 caracteres).");
    }
    if(strlen($password) < 8){
        volver_a_registro("La contraseña debe tener al menos 8 caracteres.");
    }
    if(strlen($password) > 72){
        // bcrypt solo usa los primeros 72 bytes
        volver_a_registro("La contraseña no puede superar los 72 caracteres.");
    }

    // Contraseña con bcrypt (con sal automática), en lugar de SHA-512 sin sal
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // ---- ¿Ya existe el correo o el usuario? (consultas preparadas: sin inyección SQL) ----
    $stmt = mysqli_prepare($conexion, "SELECT 1 FROM usuarios WHERE correo = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $correo);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $correo_existe = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    if($correo_existe){
        volver_a_registro("Este correo ya esta registrado, intenta con otro diferente");
    }

    $stmt = mysqli_prepare($conexion, "SELECT 1 FROM usuarios WHERE nombre_usuario = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $usuario_existe = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    if($usuario_existe){
        volver_a_registro("Este nombre de usuario ya esta registrado, intenta con otro diferente");
    }

    // ---- Inserción (una sola vez) ----
    $stmt = mysqli_prepare($conexion, "INSERT INTO usuarios (nombre_usuario, correo, password) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $usuario, $correo, $password_hash);
    $ok = mysqli_stmt_execute($stmt);
    $codigo_error = mysqli_stmt_errno($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);

    if($ok){
        header("location: ../pages/login.html");
        exit();
    }

    // 1062 = clave duplicada: dos registros simultáneos con el mismo dato
    // (lo frena el índice UNIQUE de la base de datos).
    if($codigo_error === 1062){
        volver_a_registro("Ese usuario o correo ya esta registrado, intenta con otro diferente");
    }

    error_log("Sauripedia: error al registrar usuario (código $codigo_error)");
    volver_a_registro("Error en el registro. Intenta nuevamente.");
?>
