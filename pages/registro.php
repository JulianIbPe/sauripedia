<?php
// Formulario de registro (antes "singin.html"). Lo procesa php/registro_usuario_be.php.
require_once __DIR__ . '/../includes/config.php';

if (usuario_actual() !== null) {
    redirigir('index.php');
}

$flash = tomar_flash();
$valores = $flash['valores'] ?? [];

$pagina = [
    'titulo'      => 'Registrarse | Sauripedia',
    'descripcion' => 'Crea tu cuenta en Sauripedia para dejar comentarios.',
    'ruta'        => 'pages/registro.php',
    'css'         => ['formularios.css'],
    'indexar'     => false,
];

require __DIR__ . '/../includes/cabecera.php';
?>
            <section class="panel formulario formulario_cuenta">
                <h1>Registrarse</h1>
<?php require __DIR__ . '/../includes/aviso.php'; ?>
                <form method="POST" action="<?= url('php/registro_usuario_be.php') ?>" accept-charset="UTF-8">
                    <?= campo_csrf() ?>
                    <div class="campo">
                        <label for="usuario">Nombre de usuario</label>
                        <input type="text" id="usuario" name="usuario" value="<?= e($valores['usuario'] ?? '') ?>" placeholder="Entre 3 y 50 caracteres" required minlength="3" maxlength="50" autocomplete="username">
                    </div>
                    <div class="campo">
                        <label for="correo">Correo electrónico</label>
                        <input type="email" id="correo" name="correo" value="<?= e($valores['correo'] ?? '') ?>" placeholder="nombre@ejemplo.com" required maxlength="50" autocomplete="email">
                    </div>
                    <div class="campo">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" required minlength="8" maxlength="72" autocomplete="new-password">
                    </div>
                    <button type="submit" class="boton">Crear cuenta</button>
                </form>
                <p class="formulario_pie">¿Ya tienes cuenta? <a href="<?= url('pages/login.php') ?>">Inicia sesión</a></p>
            </section>
<?php require __DIR__ . '/../includes/pie.php'; ?>
