<?php

    //session_start();

    include 'conexion_be.php';

    $usuario = $_POST['usuario'];
    $password = $_POST['password'];
    $password = hash('sha512', $password);

    $validar_login = mysqli_query($conexion, "SELECT * FROM usuarios WHERE nombre_usuario = '$usuario' AND password = '$password'");

    //falta agregar validacion de inicio de sesion, para que no entren usuarios que no esten registrados, y que no puedan entrar a la pagina principal sin iniciar sesion
    //los comentarios de aca se pueden y deben introducir de alguna manera en el codigo, no todos en este archivo
    /*
        session_start();
        if(!isset($_SESSION['usuario'])){
            echo '
                <script>
                    alert("Por favor, inicia sesion");
                    window.location = "../pages/login.html";
                </script>
            ';
            session_destroy();
            die();
        }
    */
    if(mysqli_num_rows($validar_login) > 0){
        //$_SESSION['usuario'] = $usuario;
        header("location: ../pages/index.html");
        exit();
    }
    else{
        echo '
            <script>
                alert("Usuario no existe, por favor verifique los datos ingresados");
                window.location = "../pages/login.html";
            </script>
        ';
        exit();
    }

    mysqli_close($conexion);

?>