<?php

    include 'conexion_be.php';

    $usuario = $_POST['usuario'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $password = hash('sha512', $password);

    $query = "INSERT INTO usuarios (nombre_usuario, correo, password) VALUES ('$usuario', '$correo', '$password')";

    $verificar_correo = mysqli_query($conexion, "SELECT * FROM usuarios WHERE correo = '$correo'");

    if(mysqli_num_rows($verificar_correo) > 0){
        echo '
            <script>
                alert("Este correo ya esta registrado, intenta con otro diferente");
                window.location = "../pages/singin.html";
            </script>
        ';
        exit();
    }

    $verificar_usuario = mysqli_query($conexion, "SELECT * FROM usuarios WHERE nombre_usuario = '$usuario'");

    if(mysqli_num_rows($verificar_usuario) > 0){
        echo '
            <script>
                alert("Este nombre de usuario ya esta registrado, intenta con otro diferente");
                window.location = "../pages/singin.html";
            </script>
        ';
        exit();
    }

    $ejecutar = mysqli_query($conexion, $query);
    
    if(mysqli_query($conexion, $query)){
        echo "Registro exitoso";
    }
    else{
        echo "Error en el registro";
    }

    mysqli_close($conexion);
?>