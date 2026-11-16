<?php
    // Conexión a la base de datos.
    // Los datos se pueden cambiar con variables de entorno (DB_HOST, DB_USER, DB_PASS, DB_NAME)
    // para no dejar credenciales fijas en el código cuando se suba a un servidor real.
    // Por defecto usa la configuración local de XAMPP.
    mysqli_report(MYSQLI_REPORT_OFF);

    $conexion = @mysqli_connect(
        getenv('DB_HOST') ?: 'localhost',
        getenv('DB_USER') ?: 'root',
        getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
        getenv('DB_NAME') ?: 'login_register_db'
    );

    if(!$conexion){
        // No se muestra el error real al visitante (puede revelar datos del servidor).
        error_log('Sauripedia: error de conexión a la base de datos: ' . mysqli_connect_error());
        http_response_code(500);
        echo '
            <script>
                alert("No se pudo conectar con el servidor. Intenta nuevamente más tarde.");
                window.history.back();
            </script>
        ';
        exit();
    }

    // utf8mb4 para que coincida con la tabla y evitar problemas con tildes/ñ
    mysqli_set_charset($conexion, 'utf8mb4');
?>
