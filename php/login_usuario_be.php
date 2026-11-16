<?php

    include 'config_sesion.php';
    include 'conexion_be.php';

    function volver_a_login($mensaje){
        echo '
            <script>
                alert(' . json_encode($mensaje, JSON_UNESCAPED_UNICODE) . ');
                window.location = "../pages/login.html";
            </script>
        ';
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        header("location: ../pages/login.html");
        exit();
    }

    $usuario  = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if($usuario === '' || $password === ''){
        volver_a_login("Completa usuario y contraseña.");
    }

    // ---- Límite de intentos fallidos (frena la fuerza bruta básica) ----
    $MAX_INTENTOS = 5;
    $BLOQUEO_SEG  = 300; // 5 minutos
    if(!empty($_SESSION['bloqueo_hasta']) && time() < $_SESSION['bloqueo_hasta']){
        volver_a_login("Demasiados intentos fallidos. Espera unos minutos e intenta de nuevo.");
    }

    $stmt = mysqli_prepare($conexion, "SELECT id, nombre_usuario, password FROM usuarios WHERE nombre_usuario = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $usuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);

    $valido = false;
    if($fila){
        $guardada = $fila['password'];
        if(password_verify($password, $guardada)){
            $valido = true;
            // Si el algoritmo por defecto mejoró, se actualiza el hash
            if(password_needs_rehash($guardada, PASSWORD_DEFAULT)){
                $nuevo = password_hash($password, PASSWORD_DEFAULT);
                $upd = mysqli_prepare($conexion, "UPDATE usuarios SET password = ? WHERE id = ?");
                mysqli_stmt_bind_param($upd, "si", $nuevo, $fila['id']);
                mysqli_stmt_execute($upd);
                mysqli_stmt_close($upd);
            }
        }
        // Compatibilidad: cuentas antiguas guardadas con SHA-512. Si coincide,
        // se migra automáticamente a bcrypt en este primer inicio de sesión.
        elseif(strlen($guardada) === 128 && hash_equals($guardada, hash('sha512', $password))){
            $valido = true;
            $nuevo = password_hash($password, PASSWORD_DEFAULT);
            $upd = mysqli_prepare($conexion, "UPDATE usuarios SET password = ? WHERE id = ?");
            mysqli_stmt_bind_param($upd, "si", $nuevo, $fila['id']);
            mysqli_stmt_execute($upd);
            mysqli_stmt_close($upd);
        }
    }

    mysqli_close($conexion);

    if($valido){
        session_regenerate_id(true);
        unset($_SESSION['intentos_fallidos'], $_SESSION['bloqueo_hasta']);
        // Guardamos el nombre tal cual está en la base de datos (no el que se escribió en el formulario)
        $_SESSION['usuario'] = $fila['nombre_usuario'];
        header("location: ../index.html");
        exit();
    }

    $_SESSION['intentos_fallidos'] = ($_SESSION['intentos_fallidos'] ?? 0) + 1;
    if($_SESSION['intentos_fallidos'] >= $MAX_INTENTOS){
        $_SESSION['bloqueo_hasta'] = time() + $BLOQUEO_SEG;
        $_SESSION['intentos_fallidos'] = 0;
    }
    // Mismo mensaje para "usuario inexistente" y "contraseña incorrecta"
    volver_a_login("Usuario o contraseña incorrectos, por favor verifique los datos ingresados");
?>
